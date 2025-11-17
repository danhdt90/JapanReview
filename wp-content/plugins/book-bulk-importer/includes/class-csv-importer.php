<?php
/**
 * CSV Importer Class
 * Handles CSV file parsing and validation
 */

if (!defined('ABSPATH')) {
    exit;
}

class BookBulkImporter_CsvImporter {
    
    private $required_japanese_patterns = array(
        'タイトル\[0\]\.タイトル'  // Specifically match [0] for main title
    );
    private $optional_japanese_patterns = array(
        'タイトル\[\d+\]\.タイトル',  // Match any [number] for repeatable fields
        'その他のタイトル\[\d+\]\.その他のタイトル',
        '著者\[\d+\]\.作成者姓名\.姓名',
        '内容記述\[\d+\]\.内容記述',
        '抄録\[\d+\]\.内容記述'
    );
    private $simple_japanese_fields = array(
        '資源タイプ.資源タイプ',
        'ID登録.ID登録',
        '書誌情報.巻',
        '書誌情報.発行日.日付',
        '書誌情報.開始ページ',
        '書誌情報.終了ページ'
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
                
                // First row should be headers (preserve original case for Japanese)
                if ($row_number === 1) {
                    $headers = array_map(function($header) {
                        // Remove UTF-8 BOM if present
                        $header = trim($header);
                        if (substr($header, 0, 3) === "\xEF\xBB\xBF") {
                            $header = substr($header, 3);
                        }
                        // Remove quotes if present
                        $header = trim($header, '"');
                        return $header;
                    }, $row);
                    
                    // Don't convert to lowercase for Japanese fields
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
        $has_required = false;
        
        // Check if at least one required pattern is found
        foreach ($this->required_japanese_patterns as $pattern) {
            foreach ($headers as $header) {
                $match = preg_match('/^' . $pattern . '$/', $header);
                $simple_test = ($header === 'タイトル[0].タイトル');
                
                if ($match || $simple_test) {  // Accept either regex or exact match
                    $has_required = true;
                    break 2;
                }
            }
        }
        
        // Also check for legacy title field
        if (!$has_required && !in_array('title', array_map('strtolower', $headers))) {
            $missing_required[] = 'タイトル[0].タイトル or title';
        }
        
        if (!empty($missing_required)) {
            return array(
                'success' => false,
                'message' => 'Missing required columns: ' . implode(', ', $missing_required)
            );
        }
        
        // Count recognized columns
        $recognized_count = 0;
        $warnings = array();
        
        foreach ($headers as $header) {
            $is_recognized = false;
            
            // Check Japanese patterns
            foreach (array_merge($this->required_japanese_patterns, $this->optional_japanese_patterns) as $pattern) {
                if (preg_match('/^' . $pattern . '$/', $header)) {
                    $is_recognized = true;
                    break;
                }
            }
            
            // Check simple Japanese fields
            if (!$is_recognized && in_array($header, $this->simple_japanese_fields)) {
                $is_recognized = true;
            }
            
            // Check legacy fields (case insensitive)
            if (!$is_recognized) {
                $lower_header = strtolower($header);
                if (in_array($lower_header, array('title', 'content', 'author', 'isbn', 'publication_date', 'status'))) {
                    $is_recognized = true;
                }
            }
            
            if ($is_recognized) {
                $recognized_count++;
            }
        }
        
        if ($recognized_count > 0) {
            $warnings[] = "Recognized $recognized_count out of " . count($headers) . " columns";
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
            $row_number = $index + 1; 
            $row_valid = true;
            $row_warnings = array();
            
            // Check required fields - title or Japanese main title
            $has_title = false;
            if (!empty($row['title'])) {
                $has_title = true;
            } else {
                // Check for Japanese title pattern
                foreach ($row as $key => $value) {
                    if (preg_match('/^タイトル\[\d+\]\.タイトル$/', $key) && !empty($value)) {
                        $has_title = true;
                        break;
                    }
                }
            }
            
            if (!$has_title) {
                $error_rows[] = "Row {$row_number}: Title is required (タイトル[0].タイトル or title)";
                $row_valid = false;
            }
            
            // Validate publication date format (support both YYYY-MM-DD and YYYY/MM/DD)
            $date_fields = array('publication_date', '書誌情報.発行日.日付');
            foreach ($date_fields as $date_field) {
                if (!empty($row[$date_field])) {
                    if (!$this->isValidDate($row[$date_field])) {
                        $row_warnings[] = "Row {$row_number}: Invalid date format for $date_field. Expected YYYY-MM-DD or YYYY/MM/DD";
                    }
                }
            }
            
            // Validate integer fields
            $integer_fields = array(
                '書誌情報.巻' => array('min' => 1, 'max' => 999),
                '書誌情報.開始ページ' => array(),
                '書誌情報.終了ページ' => array()
            );
            
            foreach ($integer_fields as $field => $constraints) {
                if (!empty($row[$field])) {
                    if (!is_numeric($row[$field]) || !filter_var($row[$field], FILTER_VALIDATE_INT)) {
                        $row_warnings[] = "Row {$row_number}: $field must be a valid integer";
                    } else {
                        $value = (int) $row[$field];
                        if (isset($constraints['min']) && $value < $constraints['min']) {
                            $row_warnings[] = "Row {$row_number}: $field must be at least {$constraints['min']}";
                        }
                        if (isset($constraints['max']) && $value > $constraints['max']) {
                            $row_warnings[] = "Row {$row_number}: $field must be at most {$constraints['max']}";
                        }
                    }
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
     * Validate date format (YYYY-MM-DD or YYYY/MM/DD)
     */
    private function isValidDate($date_string) {
        $formats = array('Y-m-d', 'Y/m/d');
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $date_string);
            if ($date && $date->format($format) === $date_string) {
                return true;
            }
        }
        
        return false;
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
     * Generate sample CSV content (Japanese format)
     */
    public static function getSampleCsvContent() {
        $headers = array(
            'タイトル[0].タイトル',
            'その他のタイトル[0].その他のタイトル',
            'その他のタイトル[1].その他のタイトル',
            '著者[0].作成者姓名.姓名',
            '資源タイプ.資源タイプ',
            'ID登録.ID登録',
            '内容記述[0].内容記述',
            '書誌情報.巻',
            '書誌情報.発行日.日付',
            '書誌情報.開始ページ',
            '書誌情報.終了ページ',
            '抄録[0].内容記述'
        );
        
        $sample_rows = array(
            array(
                'サンプル書籍タイトル1',
                'その他のタイトル1',
                'もう一つのタイトル1',
                '著者名1',
                '図書',
                '10.1000/sample1',
                '内容記述のサンプル1',
                '1',
                '2024/01/15',
                '1',
                '200',
                '抄録のサンプル内容1'
            ),
            array(
                'サンプル書籍タイトル2',
                'その他のタイトル2',
                '',
                '著者名2',
                '雑誌記事',
                '10.1000/sample2',
                '内容記述のサンプル2',
                '2',
                '2024/02/20',
                '5',
                '150',
                '抄録のサンプル内容2'
            ),
            array(
                'サンプル書籍タイトル3',
                '',
                '',
                '著者名3',
                '会議資料',
                '10.1000/sample3',
                '内容記述のサンプル3',
                '1',
                '2024/03/10',
                '10',
                '50',
                '抄録のサンプル内容3'
            )
        );
        
        // Use UTF-8 BOM for proper Japanese character encoding
        $csv_content = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv_content .= implode(',', array_map(function($header) { 
            return '"' . $header . '"'; 
        }, $headers)) . "\n";
        
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