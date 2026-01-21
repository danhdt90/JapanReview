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
    private $error_count = 0;
    
    /**
     * Import books from CSV data
     */
    public function importBooks($books, $update_existing = false, $dry_run = false) {
        $this->resetCounters();
        
        // Force clear all caches before starting import
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        if (empty($books)) {
            return array(
                'success' => false,
                'message' => 'No articles to import'
            );
        }
        
        foreach ($books as $index => $book_data) {
            $row_number = $index + 1; // +1 for 1-based index (header row excluded from $books array)
            
            try {
                // PRE-VALIDATE before attempting import
                $validation_result = $this->preValidateBookData($book_data, $row_number);
                
                if (!$validation_result['valid']) {
                    // Skip this record - don't import
                    $this->error_count++;
                    $this->log[] = sprintf(
                        'Row %d: ✗ Validation Failed - %s',
                        $row_number,
                        $validation_result['message']
                    );
                    continue; // Skip to next record
                }
                
                // Proceed with import
                $result = $this->importSingleBook($book_data, $update_existing, $dry_run);
                
                // Get title for logging
                $title = '';
                if (!empty($book_data['title'])) {
                    $title = $book_data['title'];
                } else {
                    $main_titles = $this->parseJapaneseRepeatableFields($book_data, 'タイトル', 'main_title');
                    if (!empty($main_titles)) {
                        $title = $main_titles[0];
                    }
                }
                
                if ($result['success']) {
                    $this->imported_count++;
                    $this->log[] = sprintf(
                        'Row %d: ✓ Created article "%s"', 
                        $row_number,
                        $title
                    );
                } else {
                    $this->error_count++;
                    $this->log[] = sprintf(
                        'Row %d: ✗ Error - %s', 
                        $row_number,
                        $result['message']
                    );
                }
            } catch (Exception $e) {
                $this->error_count++;
                $this->log[] = sprintf(
                    'Row %d: ✗ Exception - %s', 
                    $row_number,
                    $e->getMessage()
                );
            }
        }
        
        return array(
            'success' => $this->error_count === 0,
            'imported' => $this->imported_count,
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
        
        // Validate required fields - check both title and タイトル[0].タイトル
        $title = '';
        if (!empty($book_data['title'])) {
            $title = $book_data['title'];
        } else {
            // Check for Japanese main title
            $main_titles = $this->parseJapaneseRepeatableFields($book_data, 'タイトル', 'main_title');
            if (!empty($main_titles)) {
                $title = $main_titles[0];
            }
        }
        
        if (empty($title)) {
            return array(
                'success' => false,
                'message' => 'Title is required (either "title" or "タイトル[0].タイトル")'
            );
        }
        
        // Skip duplicate check - always create new article
        // No checking for existing posts
        
        if ($dry_run) {
            return array(
                'success' => true,
                'action' => 'created',
                'message' => 'Validation successful'
            );
        }
        
        // Prepare post data
        $post_data = array(
            'post_title' => sanitize_text_field($title),
            'post_content' => wp_kses_post(isset($book_data['content']) ? $book_data['content'] : ''),
            'post_type' => 'article',
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
        
        // Always insert new post (never update)
        $post_id = wp_insert_post($post_data);
        $action = 'created';
        
        if (is_wp_error($post_id)) {
            return array(
                'success' => false,
                'message' => 'Failed to save post: ' . $post_id->get_error_message()
            );
        }
        
        // Save Pods fields
        try {
            $this->savePodsFields($post_id, $book_data);
            
            // Clear various WordPress caches after saving
            $this->clearPostCaches($post_id);
            
            // Verify data was saved correctly
            $this->verifyDataSaved($post_id, $book_data);
            
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
        try {
            // === REPEATABLE FIELDS ===
            // Main Title - タイトル[0].タイトル
            // First element becomes post title, rest goes to repeater
            $main_titles = $this->parseJapaneseRepeatableFields($book_data, 'タイトル', 'main_title');
            if (!empty($main_titles)) {
                $validated_titles = array();
                foreach ($main_titles as $title) {
                    $validated_titles[] = sanitize_text_field(trim($title));
                }
                
                // Skip the first title (already used as post title)
                $repeater_titles = array_slice($validated_titles, 1);
                if (!empty($repeater_titles)) {
                    $this->saveRepeatableField($post_id, 'main_title', $repeater_titles);
                }
            }
            
            // Other Title - その他のタイトル[0].その他のタイトル
            $other_titles = $this->parseJapaneseRepeatableFields($book_data, 'その他のタイトル', 'other_title');
            if (!empty($other_titles)) {
                $validated_other_titles = array();
                foreach ($other_titles as $title) {
                    $validated_other_titles[] = sanitize_text_field(trim($title));
                }
                $this->saveRepeatableField($post_id, 'other_title', $validated_other_titles);
            }
            
            // Group Author - 著者[0].作成者姓名.姓名
            $group_authors = $this->parseNestedJapaneseRepeatableFields($book_data, '著者', '作成者姓名', '姓名');
            if (!empty($group_authors)) {
                $validated_authors = array();
                foreach ($group_authors as $author) {
                    $validated_authors[] = sanitize_text_field(trim($author));
                }
                $this->saveRepeatableField($post_id, 'group_author', $validated_authors);
            }
            
            // Content Description - 内容記述[0].内容記述
            $content_descriptions = $this->parseJapaneseRepeatableFields($book_data, '内容記述', 'content_description');
            if (!empty($content_descriptions)) {
                $validated_descriptions = array();
                foreach ($content_descriptions as $description) {
                    $validated_descriptions[] = sanitize_text_field(trim($description));
                }
                $this->saveRepeatableField($post_id, 'content_description', $validated_descriptions);
            }
            
            // Abstract - 抄録[0].内容記述
            $abstracts = array();
            $pattern = '/^抄録\[(\d+)\]\.内容記述$/u';
            foreach ($book_data as $header => $value) {
                if (preg_match($pattern, $header, $matches)) {
                    $index = (int) $matches[1];
                    $cleaned_value = trim($value);
                    if (!empty($cleaned_value)) {
                        $abstracts[$index] = $cleaned_value;
                    }
                }
            }
            if (!empty($abstracts)) {
                ksort($abstracts);
                $validated_abstracts = array();
                foreach ($abstracts as $abstract) {
                    $validated_abstracts[] = sanitize_text_field(trim($abstract));
                }
                $this->saveRepeatableField($post_id, 'abstract', $validated_abstracts);
            }
            
            // === SIMPLE FIELDS ===
            
            // Resource Type - 資源タイプ.資源タイプ
            // Save to taxonomy 'publication_type' instead of meta field
            $resource_type = $this->parseSimpleJapaneseField($book_data, '資源タイプ.資源タイプ');
            if (!empty($resource_type)) {
                $this->saveGenreTaxonomy($post_id, $resource_type);
            }
            
            // Old implementation - saved as meta field (commented out)
            // $resource_type = $this->parseSimpleJapaneseField($book_data, '資源タイプ.資源タイプ');
            // if (!empty($resource_type)) {
            //     $validated_resource_type = $this->validateString($resource_type, 255, 'resource_type', true);
            //     $this->saveSimpleField($post_id, 'resource_type', $validated_resource_type);
            // }
            
            // DOI - ID登録.ID登録
            $doi = $this->parseSimpleJapaneseField($book_data, 'ID登録.ID登録');
            if (!empty($doi)) {
                $validated_doi = $this->validateString($doi, 50, 'doi', true);
                $this->saveSimpleField($post_id, 'doi', $validated_doi);
            }
            
            // Volume - 書誌情報.巻
            $volume = $this->parseSimpleJapaneseField($book_data, '書誌情報.巻');
            if (!empty($volume)) {
                $validated_volume = $this->validateInteger($volume, 1, 999, 'volume', true);
                $this->saveSimpleField($post_id, 'volume', $validated_volume);
            }
            
            // Publication Date - 書誌情報.発行日.日付
            $publication_date = $this->parseSimpleJapaneseField($book_data, '書誌情報.発行日.日付');
            if (!empty($publication_date)) {
                $validated_date = $this->validateDate($publication_date);
                if ($validated_date) {
                    $this->saveSimpleField($post_id, 'publication_date', $validated_date);
                    
                    // Save year to taxonomy
                    $this->savePublicationYearTaxonomy($post_id, $validated_date);
                } else {
                    throw new Exception('Publication date must be in YYYY/MM/DD format');
                }
            }
            
            // Start Page - 書誌情報.開始ページ
            $start_page = $this->parseSimpleJapaneseField($book_data, '書誌情報.開始ページ');
            if (!empty($start_page)) {
                $validated_start_page = $this->validateInteger($start_page, null, null, 'start_page', true);
                $this->saveSimpleField($post_id, 'start_page', $validated_start_page);
            }
            
            // End Page - 書誌情報.終了ページ
            $end_page = $this->parseSimpleJapaneseField($book_data, '書誌情報.終了ページ');
            if (!empty($end_page)) {
                $validated_end_page = $this->validateInteger($end_page, null, null, 'end_page', true);
                $this->saveSimpleField($post_id, 'end_page', $validated_end_page);
            }
            
            // Keywords - キーワード[0].主題, キーワード[1].主題, etc.
            $keywords = array();
            $pattern = '/^キーワード\[(\d+)\]\.主題$/u';
            foreach ($book_data as $header => $value) {
                if (preg_match($pattern, $header, $matches)) {
                    $index = (int) $matches[1];
                    $cleaned_value = trim($value);
                    if (!empty($cleaned_value)) {
                        $keywords[$index] = $cleaned_value;
                    }
                }
            }
            if (!empty($keywords)) {
                ksort($keywords);
                $this->saveKeywordsTaxonomy($post_id, array_values($keywords));
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Save keywords to taxonomy
     */
    private function saveKeywordsTaxonomy($post_id, $keywords) {
        if (empty($keywords) || !is_array($keywords)) {
            return;
        }
        
        $term_ids = array();
        
        foreach ($keywords as $keyword) {
            // Skip empty keywords
            $keyword = trim($keyword);
            if (empty($keyword)) {
                continue;
            }
            
            // Check if term exists
            $term = get_term_by('name', $keyword, 'keywords_article');
            
            if (!$term) {
                // Create new term
                $result = wp_insert_term($keyword, 'keywords_article');
                
                if (is_wp_error($result)) {
                    // If term already exists (race condition), get it
                    if ($result->get_error_code() === 'term_exists') {
                        $term_id = $result->get_error_data();
                    } else {
                        // Log error but continue with other keywords
                        continue;
                    }
                } else {
                    $term_id = $result['term_id'];
                }
            } else {
                $term_id = $term->term_id;
            }
            
            $term_ids[] = (int) $term_id;
        }
        
        // Assign all keywords to the post
        if (!empty($term_ids)) {
            wp_set_object_terms($post_id, $term_ids, 'keywords_article', false);
        }
    }
    
    /**
     * Save publication type to publication_type taxonomy (single selection)
     */
    private function saveGenreTaxonomy($post_id, $genre_name) {
        if (empty($genre_name)) {
            return;
        }
        
        $genre_name = trim($genre_name);
        
        // Check if term exists
        $term = get_term_by('name', $genre_name, 'publication_type');
        
        if (!$term) {
            // Create new term
            $result = wp_insert_term($genre_name, 'publication_type');
            
            if (is_wp_error($result)) {
                // If term already exists (race condition), get it
                if ($result->get_error_code() === 'term_exists') {
                    $term_id = $result->get_error_data();
                } else {
                    // Log error and skip
                    return;
                }
            } else {
                $term_id = $result['term_id'];
            }
        } else {
            $term_id = $term->term_id;
        }
        
        // Assign publication type to the post (replace any existing type - single selection)
        if (!empty($term_id)) {
            wp_set_object_terms($post_id, array((int) $term_id), 'publication_type', false);
        }
    }
    
    /**
     * Save publication year to article_year taxonomy
     */
    private function savePublicationYearTaxonomy($post_id, $publication_date) {
        if (empty($publication_date)) {
            return;
        }
        
        // Extract year from date
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $publication_date);
        if (!$date) {
            // Try other formats
            $date = DateTime::createFromFormat('Y-m-d', $publication_date);
            if (!$date) {
                return;
            }
        }
        
        $year = $date->format('Y');
        
        // Check if term exists
        $term = get_term_by('name', $year, 'article_year');
        
        if (!$term) {
            // Create new term
            $result = wp_insert_term($year, 'article_year', array(
                'slug' => $year
            ));
            
            if (is_wp_error($result)) {
                // If term already exists (race condition), get it
                if ($result->get_error_code() === 'term_exists') {
                    $term_id = $result->get_error_data();
                } else {
                    // Log error and skip
                    return;
                }
            } else {
                $term_id = $result['term_id'];
            }
        } else {
            $term_id = $term->term_id;
        }
        
        // Assign year to the post
        if (!empty($term_id)) {
            wp_set_object_terms($post_id, array((int) $term_id), 'article_year', false);
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
                $pods = pods('article', $post_id);
                if ($pods && $pods->save($field_name, $clean_value)) {
                    return;
                }
            } catch (Exception $e) {
                // Silently catch exception and fallback to meta
            }
        }
        
        // Fallback to meta
        update_post_meta($post_id, $field_name, $clean_value);
    }
    
    /**
     * Parse nested Japanese CSV field pattern
     * Matches pattern: field_name[index].sub_field.sub_sub_field for nested repeatable fields
     * Example: 著者[0].作成者姓名.姓名, 著者[1].作成者姓名.姓名
     */
    private function parseNestedJapaneseRepeatableFields($book_data, $field_pattern, $sub_field, $final_field) {
        // Create regex pattern for nested Japanese field names
        $pattern = '/^' . preg_quote($field_pattern, '/') . '\[(\d+)\]\.' . preg_quote($sub_field, '/') . '\.' . preg_quote($final_field, '/') . '$/u';
        $indexed_data = array();
        
        // Scan all headers for matching pattern
        foreach ($book_data as $header => $value) {
            if (preg_match($pattern, $header, $matches)) {
                $index = (int) $matches[1];
                $cleaned_value = trim($value);
                
                if (!empty($cleaned_value)) {
                    $indexed_data[$index] = $cleaned_value;
                }
            }
        }
        
        if (empty($indexed_data)) {
            return array();
        }
        
        // Sort by index
        ksort($indexed_data);
        
        // Filter empty values and remove duplicates
        $cleaned_values = array_filter($indexed_data, function($value) {
            return !empty(trim($value));
        });
        
        // Remove duplicates while preserving order
        $unique_values = array();
        foreach ($cleaned_values as $value) {
            $trimmed = trim($value);
            if (!in_array($trimmed, $unique_values)) {
                $unique_values[] = $trimmed;
            }
        }
        
        return array_values($unique_values);
    }
    
    /**
     * Parse Japanese CSV field pattern
     * Matches pattern: field_name[index].field_name for repeatable fields
     * Example: タイトル[0].タイトル, その他のタイトル[1].その他のタイトル
     */
    private function parseJapaneseRepeatableFields($book_data, $field_pattern, $logical_name) {
        // Create regex pattern for Japanese field names
        $pattern = '/^' . preg_quote($field_pattern, '/') . '\[(\d+)\]\.' . preg_quote($field_pattern, '/') . '$/u';
        $indexed_data = array();
        
        // Scan all headers for matching pattern
        foreach ($book_data as $header => $value) {
            if (preg_match($pattern, $header, $matches)) {
                $index = (int) $matches[1];
                $cleaned_value = trim($value);
                
                if (!empty($cleaned_value)) {
                    $indexed_data[$index] = $cleaned_value;
                }
            }
        }
        
        if (empty($indexed_data)) {
            return array();
        }
        
        // Sort by index
        ksort($indexed_data);
        
        // Filter empty values and remove duplicates
        $cleaned_values = array_filter($indexed_data, function($value) {
            return !empty(trim($value));
        });
        
        // Remove duplicates while preserving order
        $unique_values = array();
        foreach ($cleaned_values as $value) {
            $trimmed = trim($value);
            if (!in_array($trimmed, $unique_values)) {
                $unique_values[] = $trimmed;
            }
        }
        
        return array_values($unique_values);
    }
    
    /**
     * Parse simple (non-repeatable) Japanese field
     */
    private function parseSimpleJapaneseField($book_data, $field_pattern) {
        return isset($book_data[$field_pattern]) ? trim($book_data[$field_pattern]) : '';
    }
    private function parseIndexedRepeatableFields($book_data, $field_name) {
        $pattern = '/^' . preg_quote($field_name, '/') . '\[(\d+)\]\.' . preg_quote($field_name, '/') . '$/';
        $indexed_data = array();
        
        // Scan all headers for matching pattern
        foreach ($book_data as $header => $value) {
            if (preg_match($pattern, $header, $matches)) {
                $index = (int) $matches[1];
                $cleaned_value = trim($value);
                
                if (!empty($cleaned_value) && strlen($cleaned_value) <= 255) {
                    $indexed_data[$index] = $cleaned_value;
                }
            }
        }
        
        if (empty($indexed_data)) {
            return array();
        }
        
        // Sort by index
        ksort($indexed_data);
        
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
        
        return array_values($unique_values);
    }
    
    /**
     * Save repeatable field with multiple approaches
     */
    private function saveRepeatableField($post_id, $field_name, $values) {
        if (empty($values)) {
            return;
        }
        
        // Try Pods Simple Repeatable format first
        if (class_exists('Pods')) {
            try {
                $pods = pods('article', $post_id);
                if ($pods) {
                    // Method 1: Simple array for Simple Repeatable fields
                    $clean_values = array_map('sanitize_text_field', $values);
                    $pods_result = $pods->save($field_name, $clean_values);
                    
                    if ($pods_result) {
                        return;
                    }
                }
            } catch (Exception $e) {
                // Silently catch exception and fallback to meta
            }
        }
        
        // Fallback: Direct meta approach
        delete_post_meta($post_id, $field_name);
        
        foreach ($values as $value) {
            $clean_value = sanitize_text_field($value);
            if (!empty($clean_value)) {
                add_post_meta($post_id, $field_name, $clean_value);
            }
        }
    }
    
    /**
     * Find existing article by title
     */
    private function findExistingBook($title) {
        $posts = get_posts(array(
            'post_type' => 'article',
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
     * Validate date format (YYYY/MM/DD)
     */
    private function validateDate($date_string) {
        // Support both YYYY/MM/DD and YYYY-MM-DD formats
        $formats = ['Y/m/d', 'Y-m-d'];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $date_string);
            if ($date && $date->format($format) === $date_string) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return false;
    }
    
    /**
     * Validate string field with max length
     */
    private function validateString($value, $max_length, $field_name, $required = false) {
        $value = trim($value);
        
        if (empty($value)) {
            if ($required) {
                throw new Exception("Field '$field_name' is required");
            }
            return '';
        }
        
        if (strlen($value) > $max_length) {
            throw new Exception("Field '$field_name' exceeds maximum length of $max_length characters");
        }
        
        return sanitize_text_field($value);
    }
    
    /**
     * Pre-validate book data before import
     * Returns array with 'valid' => bool and 'message' => string
     */
    private function preValidateBookData($book_data, $row_number) {
        $errors = array();
        
        // Validate Volume - 書誌情報.巻
        $volume = $this->parseSimpleJapaneseField($book_data, '書誌情報.巻');
        if (!empty($volume)) {
            $cleaned_volume = trim($volume);
            // Remove non-numeric characters
            $numeric_only = preg_replace('/[^0-9]/', '', $cleaned_volume);
            
            if ($cleaned_volume !== $numeric_only) {
                $errors[] = sprintf(
                    'Volume (書誌情報.巻) contains non-numeric characters: "%s"',
                    $volume
                );
            } elseif (!empty($numeric_only) && (!is_numeric($numeric_only) || !filter_var($numeric_only, FILTER_VALIDATE_INT))) {
                $errors[] = sprintf(
                    'Volume (書誌情報.巻) must be a valid integer: "%s"',
                    $volume
                );
            } elseif (!empty($numeric_only)) {
                $int_value = (int) $numeric_only;
                if ($int_value < 1 || $int_value > 999) {
                    $errors[] = sprintf(
                        'Volume (書誌情報.巻) must be between 1 and 999: "%s"',
                        $volume
                    );
                }
            }
        }
        
        // Validate Start Page - 書誌情報.開始ページ
        $start_page = $this->parseSimpleJapaneseField($book_data, '書誌情報.開始ページ');
        if (!empty($start_page)) {
            $cleaned_start_page = trim($start_page);
            // Remove non-numeric characters
            $numeric_only = preg_replace('/[^0-9]/', '', $cleaned_start_page);
            
            if ($cleaned_start_page !== $numeric_only) {
                $errors[] = sprintf(
                    'Start Page (書誌情報.開始ページ) contains non-numeric characters: "%s"',
                    $start_page
                );
            } elseif (!empty($numeric_only) && (!is_numeric($numeric_only) || !filter_var($numeric_only, FILTER_VALIDATE_INT))) {
                $errors[] = sprintf(
                    'Start Page (書誌情報.開始ページ) must be a valid integer: "%s"',
                    $start_page
                );
            }
        }
        
        // Validate End Page - 書誌情報.終了ページ
        $end_page = $this->parseSimpleJapaneseField($book_data, '書誌情報.終了ページ');
        if (!empty($end_page)) {
            $cleaned_end_page = trim($end_page);
            // Remove non-numeric characters
            $numeric_only = preg_replace('/[^0-9]/', '', $cleaned_end_page);
            
            if ($cleaned_end_page !== $numeric_only) {
                $errors[] = sprintf(
                    'End Page (書誌情報.終了ページ) contains non-numeric characters: "%s"',
                    $end_page
                );
            } elseif (!empty($numeric_only) && (!is_numeric($numeric_only) || !filter_var($numeric_only, FILTER_VALIDATE_INT))) {
                $errors[] = sprintf(
                    'End Page (書誌情報.終了ページ) must be a valid integer: "%s"',
                    $end_page
                );
            }
        }
        
        // Validate Title (required)
        $title = '';
        if (!empty($book_data['title'])) {
            $title = $book_data['title'];
        } else {
            $main_titles = $this->parseJapaneseRepeatableFields($book_data, 'タイトル', 'main_title');
            if (!empty($main_titles)) {
                $title = $main_titles[0];
            }
        }
        
        if (empty($title)) {
            $errors[] = 'Title is required (either "title" or "タイトル[0].タイトル")';
        }
        
        // Validate Date format if provided
        $publication_date = $this->parseSimpleJapaneseField($book_data, '書誌情報.発行日.日付');
        if (!empty($publication_date)) {
            if (!$this->validateDate($publication_date)) {
                $errors[] = sprintf(
                    'Publication date (書誌情報.発行日.日付) must be in YYYY/MM/DD or YYYY-MM-DD format: "%s"',
                    $publication_date
                );
            }
        }
        
        if (!empty($errors)) {
            return array(
                'valid' => false,
                'message' => implode('; ', $errors)
            );
        }
        
        return array(
            'valid' => true,
            'message' => ''
        );
    }
    
    /**
     * Validate integer field with range
     */
    private function validateInteger($value, $min = null, $max = null, $field_name = '', $required = false) {
        $value = trim($value);
        
        if (empty($value)) {
            if ($required) {
                throw new Exception("Field '$field_name' is required");
            }
            return null;
        }
        
        // Clean non-numeric characters (already validated in preValidateBookData)
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        
        if (empty($cleaned)) {
            if ($required) {
                throw new Exception("Field '$field_name' must be a valid integer");
            }
            return null;
        }
        
        if (!is_numeric($cleaned) || !filter_var($cleaned, FILTER_VALIDATE_INT)) {
            throw new Exception("Field '$field_name' must be a valid integer");
        }
        
        $int_value = (int) $cleaned;
        
        if ($min !== null && $int_value < $min) {
            throw new Exception("Field '$field_name' must be at least $min");
        }
        
        if ($max !== null && $int_value > $max) {
            throw new Exception("Field '$field_name' must be at most $max");
        }
        
        return $int_value;
    }
    
    /**
     * Reset import counters
     */
    private function resetCounters() {
        $this->log = array();
        $this->imported_count = 0;
        $this->error_count = 0;
    }
    
    /**
     * Generate summary message
     */
    private function generateSummaryMessage($dry_run) {
        if ($dry_run) {
            return sprintf(
                'Validation completed. Would import %d articles. %d errors found.',
                $this->imported_count,
                $this->error_count
            );
        }
        
        return sprintf(
            'Import completed. Imported %d articles, %d errors.',
            $this->imported_count,
            $this->error_count
        );
    }
    
    /**
     * Verify that data was saved correctly after import
     */
    private function verifyDataSaved($post_id, $book_data) {
        // Verification removed - silent operation
    }
    
    /**
     * Clear various caches for a post after import
     */
    private function clearPostCaches($post_id) {
        // Clear WordPress object cache
        wp_cache_delete($post_id, 'posts');
        wp_cache_delete($post_id, 'post_meta');
        
        // Clear Pods cache if available
        if (class_exists('Pods')) {
            $pods = pods('book', $post_id);
            if ($pods && method_exists($pods, 'clear_cache')) {
                $pods->clear_cache();
            }
        }
        
        // Clear any transient caches
        delete_transient('pods_cache_' . $post_id);
        
        // Clear meta cache
        wp_cache_delete($post_id, 'meta_objects');
        clean_post_cache($post_id);
    }
}