<?php
/**
 * Book Importer Class
 * Handles the core logic for importing book posts with Pods fields
 */

if (!defined('ABSPATH')) {
    exit;
}

class BookBulkImporter_BookImporter {
    
    private $log = array();
    private $imported_count = 0;
    private $updated_count = 0;
    private $error_count = 0;
    
    /**
     * Import books from CSV data
     */
    public function importBooks($books, $update_existing = false, $dry_run = false) {
        $this->resetCounters();
        
        if (empty($books)) {
            return array(
                'success' => false,
                'message' => 'No books to import'
            );
        }
        
        foreach ($books as $index => $book_data) {
            try {
                $result = $this->importSingleBook($book_data, $update_existing, $dry_run);
                
                if ($result['success']) {
                    if ($result['action'] === 'created') {
                        $this->imported_count++;
                        $this->log[] = sprintf(
                            'Row %d: Created book "%s"', 
                            $index + 2,
                            $book_data['title']
                        );
                    } else {
                        $this->updated_count++;
                        $this->log[] = sprintf(
                            'Row %d: Updated book "%s"', 
                            $index + 2,
                            $book_data['title']
                        );
                    }
                } else {
                    $this->error_count++;
                    $this->log[] = sprintf(
                        'Row %d: Error - %s', 
                        $index + 2,
                        $result['message']
                    );
                }
            } catch (Exception $e) {
                $this->error_count++;
                $this->log[] = sprintf(
                    'Row %d: Exception - %s', 
                    $index + 2,
                    $e->getMessage()
                );
            }
        }
        
        return array(
            'success' => $this->error_count === 0,
            'imported' => $this->imported_count,
            'updated' => $this->updated_count,
            'errors' => $this->error_count,
            'log' => $this->log,
            'dry_run' => $dry_run,
            'message' => $this->generateSummaryMessage($dry_run)
        );
    }
    
    /**
     * Import single book
     */
    private function importSingleBook($book_data, $update_existing, $dry_run) {
        // Validate required fields
        if (empty($book_data['title'])) {
            return array(
                'success' => false,
                'message' => 'Title is required'
            );
        }
        
        // Check if book already exists
        $existing_post = $this->findExistingBook($book_data['title']);
        
        if ($existing_post && !$update_existing) {
            return array(
                'success' => false,
                'message' => 'Book already exists and update_existing is disabled'
            );
        }
        
        if ($dry_run) {
            $action = $existing_post ? 'updated' : 'created';
            return array(
                'success' => true,
                'action' => $action,
                'message' => 'Validation successful'
            );
        }
        
        // Prepare post data
        $post_data = array(
            'post_title' => sanitize_text_field($book_data['title']),
            'post_content' => wp_kses_post(isset($book_data['content']) ? $book_data['content'] : ''),
            'post_type' => 'book',
            'post_status' => $this->sanitizePostStatus(isset($book_data['status']) ? $book_data['status'] : 'publish'),
            'post_author' => get_current_user_id(),
        );
        
        // Add publication date if provided
        if (!empty($book_data['publication_date'])) {
            $date = $this->validateDate($book_data['publication_date']);
            if ($date) {
                $post_data['post_date'] = $date;
            }
        }
        
        // Insert or update post
        if ($existing_post) {
            $post_data['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_data);
            $action = 'updated';
        } else {
            $post_id = wp_insert_post($post_data);
            $action = 'created';
        }
        
        if (is_wp_error($post_id)) {
            return array(
                'success' => false,
                'message' => 'Failed to save post: ' . $post_id->get_error_message()
            );
        }
        
        // Save Pods fields
        try {
            $this->savePodsFields($post_id, $book_data);
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Failed to save Pods fields: ' . $e->getMessage()
            );
        }
        
        return array(
            'success' => true,
            'action' => $action,
            'post_id' => $post_id
        );
    }
    
    /**
     * Save Pods fields for the book
     */
    private function savePodsFields($post_id, $book_data) {
        // Log for debugging
        error_log("Saving Pods fields for post $post_id with data: " . print_r($book_data, true));
        
        // Save simple fields
        if (!empty($book_data['author'])) {
            $this->saveSimpleField($post_id, 'author', $book_data['author']);
        }
        
        if (!empty($book_data['isbn'])) {
            $this->saveSimpleField($post_id, 'isbn', $book_data['isbn']);
        }
        
        // Save repeatable field using new indexed format
        $other_titles = $this->parseIndexedRepeatableFields($book_data, 'other_title');
        
        // Fallback to old pipe-separated format if no indexed fields found
        if (empty($other_titles) && !empty($book_data['other_title'])) {
            $other_titles = $this->parseRepeatableField($book_data['other_title']);
        }
        
        if (!empty($other_titles)) {
            $this->saveRepeatableField($post_id, 'other_title', $other_titles);
        }
    }
    
    /**
     * Save simple (non-repeatable) field
     */
    private function saveSimpleField($post_id, $field_name, $value) {
        $clean_value = sanitize_text_field($value);
        
        // Try Pods first
        if (class_exists('Pods')) {
            try {
                $pods = pods('book', $post_id);
                if ($pods && $pods->save($field_name, $clean_value)) {
                    error_log("Saved $field_name via Pods: $clean_value");
                    return;
                }
            } catch (Exception $e) {
                error_log("Pods save failed for $field_name: " . $e->getMessage());
            }
        }
        
        // Fallback to meta
        update_post_meta($post_id, $field_name, $clean_value);
        error_log("Saved $field_name via meta: $clean_value");
    }
    
    /**
     * Parse indexed repeatable fields from CSV columns
     * Matches pattern: field_name[index].field_name
     * Example: other_title[0].other_title, other_title[1].other_title
     */
    private function parseIndexedRepeatableFields($book_data, $field_name) {
        $pattern = '/^' . preg_quote($field_name, '/') . '\[(\d+)\]\.' . preg_quote($field_name, '/') . '$/';
        $indexed_data = array();
        
        error_log("Searching for indexed fields matching pattern: $pattern");
        
        // Scan all headers for matching pattern
        foreach ($book_data as $header => $value) {
            if (preg_match($pattern, $header, $matches)) {
                $index = (int) $matches[1];
                $cleaned_value = trim($value);
                
                error_log("Found indexed field: $header = '$value' (index: $index)");
                
                if (!empty($cleaned_value) && strlen($cleaned_value) <= 255) {
                    $indexed_data[$index] = $cleaned_value;
                }
            }
        }
        
        if (empty($indexed_data)) {
            error_log("No indexed fields found for $field_name");
            return array();
        }
        
        // Sort by index
        ksort($indexed_data);
        error_log("Sorted indexed data: " . print_r($indexed_data, true));
        
        // Filter empty values and remove duplicates
        $cleaned_values = array_filter($indexed_data, function($value) {
            return !empty(trim($value));
        });
        
        // Optional: Remove duplicates while preserving order
        $unique_values = array();
        foreach ($cleaned_values as $value) {
            $trimmed = trim($value);
            if (!in_array($trimmed, $unique_values)) {
                $unique_values[] = $trimmed;
            }
        }
        
        error_log("Final parsed values: " . print_r($unique_values, true));
        return array_values($unique_values);
    }
    
    /**
     * Save repeatable field with multiple approaches
     */
    private function saveRepeatableField($post_id, $field_name, $values) {
        if (empty($values)) {
            error_log("No values to save for repeatable field $field_name");
            return;
        }
        
        if (count($values) > 10) {
            throw new Exception('Too many ' . $field_name . ' values (maximum 10 allowed)');
        }
        
        error_log("Saving repeatable field $field_name with values: " . print_r($values, true));
        
        // Try Pods Simple Repeatable format first
        if (class_exists('Pods')) {
            try {
                $pods = pods('book', $post_id);
                if ($pods) {
                    // Method 1: Simple array for Simple Repeatable fields
                    $clean_values = array_map('sanitize_text_field', $values);
                    $pods_result = $pods->save($field_name, $clean_values);
                    
                    if ($pods_result) {
                        error_log("Successfully saved via Pods simple array format");
                        
                        // Verify the save
                        $saved_data = $pods->field($field_name);
                        error_log("Verification - Pods field data: " . print_r($saved_data, true));
                        return;
                    } else {
                        error_log("Pods simple array save failed, trying alternatives");
                    }
                }
            } catch (Exception $e) {
                error_log("Pods save failed with exception: " . $e->getMessage());
            }
        }
        
        // Fallback: Direct meta approach
        error_log("Using fallback meta approach for $field_name");
        delete_post_meta($post_id, $field_name);
        
        foreach ($values as $value) {
            $clean_value = sanitize_text_field($value);
            if (!empty($clean_value)) {
                $meta_result = add_post_meta($post_id, $field_name, $clean_value);
                error_log("Meta save for '$clean_value': " . ($meta_result ? 'SUCCESS' : 'FAILED'));
            }
        }
        
        // Final verification
        $final_meta = get_post_meta($post_id, $field_name);
        error_log("Final meta verification: " . print_r($final_meta, true));
    }
    
    /**
     * Parse repeatable field data (pipe-separated values) - Legacy format
     */
    private function parseRepeatableField($field_data) {
        if (empty($field_data)) {
            return array();
        }
        
        if (is_array($field_data)) {
            $field_data = implode('|', $field_data);
        }
        
        $field_data = (string) $field_data;
        $values = explode('|', $field_data);
        $cleaned_values = array();
        
        foreach ($values as $value) {
            $cleaned_value = trim($value);
            if (!empty($cleaned_value) && strlen($cleaned_value) <= 255) {
                $cleaned_values[] = $cleaned_value;
            }
        }
        
        return array_values(array_unique($cleaned_values));
    }
    
    /**
     * Find existing book by title
     */
    private function findExistingBook($title) {
        $posts = get_posts(array(
            'post_type' => 'book',
            'title' => $title,
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => 1
        ));
        
        return !empty($posts) ? $posts[0] : null;
    }
    
    /**
     * Validate and sanitize post status
     */
    private function sanitizePostStatus($status) {
        $valid_statuses = array('publish', 'draft', 'private');
        $status = strtolower(trim($status));
        
        return in_array($status, $valid_statuses) ? $status : 'publish';
    }
    
    /**
     * Validate date format
     */
    private function validateDate($date_string) {
        $date = DateTime::createFromFormat('Y-m-d', $date_string);
        
        if ($date && $date->format('Y-m-d') === $date_string) {
            return $date->format('Y-m-d H:i:s');
        }
        
        return false;
    }
    
    /**
     * Reset import counters
     */
    private function resetCounters() {
        $this->log = array();
        $this->imported_count = 0;
        $this->updated_count = 0;
        $this->error_count = 0;
    }
    
    /**
     * Generate summary message
     */
    private function generateSummaryMessage($dry_run) {
        if ($dry_run) {
            return sprintf(
                'Validation completed. Would import %d books, update %d books. %d errors found.',
                $this->imported_count,
                $this->updated_count,
                $this->error_count
            );
        }
        
        return sprintf(
            'Import completed. Imported %d books, updated %d books, %d errors.',
            $this->imported_count,
            $this->updated_count,
            $this->error_count
        );
    }
}