<?php
/**
 * CSV Importer Class
 * Handles CSV file parsing and validation
 */

if (!defined('ABSPATH')) {
    exit;
}

class BookBulkImporter_CsvImporter {
    
    private $required_columns = array('title');
    private $optional_columns = array('content', 'author', 'other_title', 'isbn', 'publication_date', 'status');
    private $indexed_field_patterns = array(
        'other_title' => '/^other_title\[\d+\]\.other_title$/'
    );
    private $max_file_size = 2097152; // 2MB in bytes
    
    /**
     * Validate CSV file
     */
    public function validateFile($file_path) {
        // Check file exists and is readable
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return array(
                'success' => false,
                'message' => 'File is not readable'
            );
        }
        
        // Check file size
        if (filesize($file_path) > $this->max_file_size) {
            return array(
                'success' => false,
                'message' => 'File is too large. Maximum size is 2MB'
            );
        }
        
        // Parse and validate CSV structure
        $parse_result = $this->parseFile($file_path, true); // validation mode
        
        if (!$parse_result['success']) {
            return $parse_result;
        }
        
        $data = $parse_result['data'];
        $headers = $parse_result['headers'];
        
        // Validate headers
        $header_validation = $this->validateHeaders($headers);
        if (!$header_validation['success']) {
            return $header_validation;
        }
        
        // Validate data rows
        $data_validation = $this->validateData($data);
        if (!$data_validation['success']) {
            return $data_validation;
        }
        
        return array(
            'success' => true,
            'message' => 'CSV file is valid',
            'headers' => $headers,
            'row_count' => count($data),
            'preview' => array_slice($data, 0, 5), // Show first 5 rows as preview
            'validation_details' => array(
                'total_rows' => count($data),
                'valid_rows' => $data_validation['valid_count'],
                'warnings' => $data_validation['warnings']
            )
        );
    }
    
    /**
     * Parse CSV file
     */
    public function parseFile($file_path, $validation_mode = false) {
        $handle = fopen($file_path, 'r');
        
        if (!$handle) {
            return array(
                'success' => false,
                'message' => 'Cannot open CSV file'
            );
        }
        
        $data = array();
        $headers = array();
        $row_number = 0;
        
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $row_number++;
                
                // First row should be headers
                if ($row_number === 1) {
                    $headers = array_map('trim', $row);
                    $headers = array_map('strtolower', $headers);
                    continue;
                }
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Map row data to headers
                $row_data = array();
                foreach ($headers as $index => $header) {
                    $row_data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }
                
                // Debug log for other_title field
                if (isset($row_data['other_title']) && !empty($row_data['other_title'])) {
                    $log_file = WP_CONTENT_DIR . '/book-importer-debug.log';
                    $timestamp = date('Y-m-d H:i:s');
                    $log_entry = "[$timestamp] CSV Row $row_number - other_title: " . var_export($row_data['other_title'], true) . "\n";
                    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
                }
                
                $data[] = $row_data;
                
                // In validation mode, limit to first 100 rows for performance
                if ($validation_mode && count($data) >= 100) {
                    break;
                }
            }
            
            fclose($handle);
            
            if (empty($headers)) {
                return array(
                    'success' => false,
                    'message' => 'CSV file appears to be empty or invalid'
                );
            }
            
            return array(
                'success' => true,
                'data' => $data,
                'headers' => $headers
            );
            
        } catch (Exception $e) {
            fclose($handle);
            return array(
                'success' => false,
                'message' => 'Error parsing CSV: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Validate CSV headers
     */
    private function validateHeaders($headers) {
        $missing_required = array();
        
        // Check required columns
        foreach ($this->required_columns as $required) {
            if (!in_array($required, $headers)) {
                $missing_required[] = $required;
            }
        }
        
        if (!empty($missing_required)) {
            return array(
                'success' => false,
                'message' => 'Missing required columns: ' . implode(', ', $missing_required)
            );
        }
        
        // Check for unknown columns
        $all_columns = array_merge($this->required_columns, $this->optional_columns);
        $unknown_columns = array();
        $indexed_columns = array();
        
        foreach ($headers as $header) {
            if (!in_array($header, $all_columns)) {
                // Check if it matches any indexed field pattern
                $is_indexed = false;
                foreach ($this->indexed_field_patterns as $field_name => $pattern) {
                    if (preg_match($pattern, $header)) {
                        $indexed_columns[] = $header;
                        $is_indexed = true;
                        break;
                    }
                }
                
                if (!$is_indexed) {
                    $unknown_columns[] = $header;
                }
            }
        }
        
        $warnings = array();
        if (!empty($unknown_columns)) {
            $warnings[] = 'Unknown columns will be ignored: ' . implode(', ', $unknown_columns);
        }
        if (!empty($indexed_columns)) {
            $warnings[] = 'Found indexed field columns: ' . implode(', ', $indexed_columns);
        }
        
        return array(
            'success' => true,
            'warnings' => $warnings
        );
    }
    
    /**
     * Validate data rows
     */
    private function validateData($data) {
        $warnings = array();
        $valid_count = 0;
        $error_rows = array();
        
        foreach ($data as $index => $row) {
            $row_number = $index + 2; // +2 because index starts at 0 and we skip header row
            $row_valid = true;
            $row_warnings = array();
            
            // Check required fields
            if (empty($row['title'])) {
                $error_rows[] = "Row {$row_number}: Title is required";
                $row_valid = false;
            }
            
            // Validate publication date format
            if (!empty($row['publication_date'])) {
                if (!$this->isValidDate($row['publication_date'])) {
                    $row_warnings[] = "Row {$row_number}: Invalid date format for publication_date. Expected YYYY-MM-DD";
                }
            }
            
            // Validate status
            if (!empty($row['status'])) {
                $valid_statuses = array('publish', 'draft', 'private');
                if (!in_array(strtolower($row['status']), $valid_statuses)) {
                    $row_warnings[] = "Row {$row_number}: Invalid status '{$row['status']}'. Will default to 'publish'";
                }
            }
            
            // Validate ISBN format (basic check)
            if (!empty($row['isbn'])) {
                if (!$this->isValidISBN($row['isbn'])) {
                    $row_warnings[] = "Row {$row_number}: ISBN format may be invalid";
                }
            }
            
            if ($row_valid) {
                $valid_count++;
            }
            
            if (!empty($row_warnings)) {
                $warnings = array_merge($warnings, $row_warnings);
            }
        }
        
        if (!empty($error_rows)) {
            return array(
                'success' => false,
                'message' => 'Data validation failed',
                'errors' => $error_rows,
                'warnings' => $warnings
            );
        }
        
        return array(
            'success' => true,
            'valid_count' => $valid_count,
            'warnings' => $warnings
        );
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    private function isValidDate($date_string) {
        $date = DateTime::createFromFormat('Y-m-d', $date_string);
        return $date && $date->format('Y-m-d') === $date_string;
    }
    
    /**
     * Basic ISBN validation
     */
    private function isValidISBN($isbn) {
        // Remove hyphens and spaces
        $isbn = preg_replace('/[^0-9X]/i', '', $isbn);
        
        // Check length (10 or 13 digits)
        $length = strlen($isbn);
        return ($length === 10 || $length === 13);
    }
    
    /**
     * Generate sample CSV content (using indexed column format)
     */
    public static function getSampleCsvContent() {
        $headers = array(
            'title', 
            'content', 
            'author', 
            'other_title[0].other_title',
            'other_title[1].other_title',
            'other_title[2].other_title',
            'isbn', 
            'publication_date', 
            'status'
        );
        
        $sample_rows = array(
            array(
                'Sample Book 1',
                'This is the description for sample book 1',
                'Author Name 1',
                'Alternative Title 1',
                'Other Title 1',
                'Third Title 1',
                '9780123456789',
                '2024-01-15',
                'publish'
            ),
            array(
                'Sample Book 2',
                'This is the description for sample book 2',
                'Author Name 2',
                'Alternative Title 2',
                'Other Title 2',
                '', // Empty third title
                '9780987654321',
                '2024-02-20',
                'draft'
            ),
            array(
                'Sample Book 3',
                'This is the description for sample book 3',
                'Author Name 3',
                'Single Alternative Title',
                '', // Empty second title
                '', // Empty third title
                '9781122334455',
                '2024-03-10',
                'publish'
            )
        );
        
        $csv_content = implode(',', $headers) . "\n";
        
        foreach ($sample_rows as $row) {
            $csv_content .= '"' . implode('","', $row) . '"' . "\n";
        }
        
        return $csv_content;
    }
    
    /**
     * Generate legacy sample CSV content (using pipe-separated format)
     */
    public static function getLegacySampleCsvContent() {
        $headers = array('title', 'content', 'author', 'other_title', 'isbn', 'publication_date', 'status');
        
        $sample_rows = array(
            array(
                'Sample Book 1',
                'This is the description for sample book 1',
                'Author Name 1',
                'Alternative Title 1|Other Title 1|Third Title 1',
                '9780123456789',
                '2024-01-15',
                'publish'
            ),
            array(
                'Sample Book 2',
                'This is the description for sample book 2',
                'Author Name 2',
                'Alternative Title 2|Other Title 2',
                '9780987654321',
                '2024-02-20',
                'draft'
            ),
            array(
                'Sample Book 3',
                'This is the description for sample book 3',
                'Author Name 3',
                'Single Alternative Title',
                '9781122334455',
                '2024-03-10',
                'publish'
            )
        );
        
        $csv_content = implode(',', $headers) . "\n";
        
        foreach ($sample_rows as $row) {
            $csv_content .= '"' . implode('","', $row) . '"' . "\n";
        }
        
        return $csv_content;
    }
}