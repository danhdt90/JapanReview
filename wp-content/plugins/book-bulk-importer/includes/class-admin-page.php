<?php
/**
 * Admin Page Class for Article Bulk Importer
 */

if (!defined('ABSPATH')) {
    exit;
}

class BookBulkImporter_AdminPage {
    
    public function __construct() {
        add_action('wp_ajax_book_bulk_import', array($this, 'handleImport'));
        add_action('wp_ajax_book_validate_csv', array($this, 'validateCsv'));
        add_action('wp_ajax_book_download_sample_csv', array($this, 'downloadSampleCsv'));
    }
    
    /**
     * Render admin page
     */
    public function render() {
        ?>
        <div class="wrap">
            <h1><?php _e('Article Bulk Importer', 'book-bulk-importer'); ?></h1>
            
            <div class="book-import-container">
                <div class="book-import-section">
                    <h2><?php _e('CSV Format Guidelines', 'book-bulk-importer'); ?></h2>
                    <div class="repeatable-field-info">
                        <h3><?php _e('Repeatable Fields Format', 'book-bulk-importer'); ?></h3>
                        <p><?php _e('For repeatable fields, use indexed columns:', 'book-bulk-importer'); ?></p>
                        <div class="examples">
                            <h4><?php _e('Example 1: Multiple Titles', 'book-bulk-importer'); ?></h4>
                            <code>タイトル[0].タイトル, タイトル[1].タイトル, タイトル[2].タイトル</code>
                            
                            <h4><?php _e('Example 2: Multiple Content Descriptions', 'book-bulk-importer'); ?></h4>
                            <code>内容記述[0].内容記述, 内容記述[1].内容記述, 内容記述[2].内容記述</code>
                        </div>
                    </div>
                    
                    <div class="example-csv">
                        <h3><?php _e('Example CSV Content:', 'book-bulk-importer'); ?></h3>
                        <div style="overflow-x: auto; font-family: monospace; background: #f5f5f5; padding: 10px; border: 1px solid #ddd;">
                            <div>タイトル[0].タイトル,その他のタイトル[0].その他のタイトル,著者[0].作成者姓名.姓名,資源タイプ.資源タイプ,ID登録.ID登録,内容記述[0].内容記述,書誌情報.巻,書誌情報.発行日.日付,書誌情報.開始ページ,書誌情報.終了ページ,抄録[0].内容記述,キーワード[0].主題,キーワード[1].主題,キーワード[2].主題</div>
                            <div>"Sample Article Title","Alternative Title","Author Name","Article","10.1000/sample","Content description",1,2024/01/15,1,200,"Abstract content","Japan","History","Culture"</div>
                            <div>"Another Article","Second Alt Title","Another Author","Article","10.1000/sample2","Another description",2,2024/02/20,5,150,"Another abstract","Economics","Policy","Research"</div>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="#" class="button download-sample-csv" data-format="indexed"><?php _e('Download Sample CSV', 'book-bulk-importer'); ?></a>
                        </div>
                    </div>
                </div>
                
                <div class="book-import-section">
                    <h2><?php _e('Import CSV File', 'book-bulk-importer'); ?></h2>
                    
                    <form id="book-import-form" enctype="multipart/form-data">
                        <?php wp_nonce_field('book_bulk_importer_nonce', 'book_import_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="csv_file"><?php _e('CSV File', 'book-bulk-importer'); ?></label>
                                </th>
                                <td>
                                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required />
                                    <p class="description"><?php _e('Select a CSV file to import articles.', 'book-bulk-importer'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <!-- <th scope="row">
                                    <label for="dry_run"><?php _e('Test Run', 'book-bulk-importer'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" id="dry_run" name="dry_run" value="1" />
                                    <label for="dry_run"><?php _e('Perform a test run (validate data without importing)', 'book-bulk-importer'); ?></label>
                                </td> -->
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="button" id="validate-csv" class="button"><?php _e('Validate CSV', 'book-bulk-importer'); ?></button>
                            <button type="submit" id="import-books" class="button-primary" disabled><?php _e('Import Articles', 'book-bulk-importer'); ?></button>
                        </p>
                    </form>
                </div>
                
                <div class="book-import-section" id="validation-results" style="display: none;">
                    <h2><?php _e('Validation Results', 'book-bulk-importer'); ?></h2>
                    <div id="validation-content"></div>
                </div>
                
                <div class="book-import-section" id="import-progress" style="display: none;">
                    <h2><?php _e('Import Progress', 'book-bulk-importer'); ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div id="import-status"></div>
                    <div id="import-log"></div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Validate CSV file via AJAX
     */
    public function validateCsv() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'book_bulk_importer_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check file upload
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('File upload failed');
        }
        
        $file = $_FILES['csv_file'];
        
        // Validate file type
        $file_info = pathinfo($file['name']);
        if (strtolower($file_info['extension']) !== 'csv') {
            wp_send_json_error('Please upload a CSV file');
        }
        
        try {
            // Initialize CSV importer
            $csv_importer = new BookBulkImporter_CsvImporter();
            $validation_result = $csv_importer->validateFile($file['tmp_name']);
            
            if ($validation_result['success']) {
                wp_send_json_success($validation_result);
            } else {
                wp_send_json_error($validation_result['message'], $validation_result);
            }
        } catch (Exception $e) {
            wp_send_json_error('Validation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle import via AJAX
     */
    public function handleImport() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'book_bulk_importer_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check file upload
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('File upload failed');
        }
        
        $file = $_FILES['csv_file'];
        $update_existing = false; // Always import as new, never update existing articles
        $dry_run = isset($_POST['dry_run']) && $_POST['dry_run'] === '1';
        
        try {
            // Initialize importers
            $csv_importer = new BookBulkImporter_CsvImporter();
            $book_importer = new BookBulkImporter_BookImporter();
            
            // Parse CSV
            $csv_data = $csv_importer->parseFile($file['tmp_name']);
            
            if (!$csv_data['success']) {
                wp_send_json_error($csv_data['message']);
            }
            
            // Import articles
            $import_result = $book_importer->importBooks(
                $csv_data['data'], 
                $update_existing, 
                $dry_run
            );
            
            if ($import_result['success']) {
                wp_send_json_success($import_result);
            } else {
                wp_send_json_error($import_result['message'], $import_result);
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Import failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download sample CSV file
     */
    public function downloadSampleCsv() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'book_bulk_importer_nonce')) {
            wp_die('Security check failed');
        }
        
        $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : 'indexed';
        
        try {
            $csv_content = BookBulkImporter_CsvImporter::getSampleCsvContent();
            $filename = 'sample-articles.csv';
            
            wp_send_json_success(array(
                'content' => $csv_content,
                'filename' => $filename
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('Failed to generate sample CSV: ' . $e->getMessage());
        }
    }
}