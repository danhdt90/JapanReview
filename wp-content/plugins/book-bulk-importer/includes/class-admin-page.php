<?php
/**
 * Admin Page Class for Book Bulk Importer
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
            <h1><?php _e('Book Bulk Importer', 'book-bulk-importer'); ?></h1>
            
            <div class="book-import-container">
                <div class="book-import-section">
                    <h2><?php _e('CSV Format Guidelines', 'book-bulk-importer'); ?></h2>
                    <p><?php _e('Your CSV file should have the following columns:', 'book-bulk-importer'); ?></p>
                    <ul>
                        <li><strong>title</strong> - <?php _e('Book title (required)', 'book-bulk-importer'); ?></li>
                        <li><strong>content</strong> - <?php _e('Book description/content', 'book-bulk-importer'); ?></li>
                        <li><strong>author</strong> - <?php _e('Book author', 'book-bulk-importer'); ?></li>
                        <li><strong>other_title</strong> - <?php _e('Alternative titles (see formats below)', 'book-bulk-importer'); ?></li>
                        <li><strong>isbn</strong> - <?php _e('ISBN number', 'book-bulk-importer'); ?></li>
                        <li><strong>publication_date</strong> - <?php _e('Publication date (YYYY-MM-DD format)', 'book-bulk-importer'); ?></li>
                        <li><strong>status</strong> - <?php _e('Post status (publish, draft, private)', 'book-bulk-importer'); ?></li>
                    </ul>
                    
                    <div class="alternative-title-formats">
                        <h3><?php _e('Alternative Title Formats', 'book-bulk-importer'); ?></h3>
                        <div style="margin-bottom: 15px;">
                            <h4><?php _e('Method 1: Indexed Columns (Recommended)', 'book-bulk-importer'); ?></h4>
                            <p><?php _e('Use separate columns for each alternative title:', 'book-bulk-importer'); ?></p>
                            <code>other_title[0].other_title,other_title[1].other_title,other_title[2].other_title</code>
                        </div>
                        <div>
                            <h4><?php _e('Method 2: Pipe-Separated (Legacy)', 'book-bulk-importer'); ?></h4>
                            <p><?php _e('Use a single column with pipe-separated values:', 'book-bulk-importer'); ?></p>
                            <code>"Title1|Title2|Title3"</code>
                        </div>
                    </div>
                    
                    <div class="example-csv">
                        <h3><?php _e('Example CSV Content (Indexed Format):', 'book-bulk-importer'); ?></h3>
                        <code>
                            title,author,other_title[0].other_title,other_title[1].other_title,other_title[2].other_title,isbn<br>
                            "Sample Book 1","Author 1","Alt Title 1","Alt Title 2","Alt Title 3","9780123456789"<br>
                            "Sample Book 2","Author 2","Single Alt Title","","","9780987654321"
                        </code>
                        <div style="margin-top: 15px;">
                            <a href="#" class="button download-sample-csv" data-format="indexed"><?php _e('Download Sample CSV (Indexed)', 'book-bulk-importer'); ?></a>
                            <a href="#" class="button download-sample-csv" data-format="legacy"><?php _e('Download Sample CSV (Legacy)', 'book-bulk-importer'); ?></a>
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
                                    <p class="description"><?php _e('Select a CSV file to import books.', 'book-bulk-importer'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="update_existing"><?php _e('Update Existing', 'book-bulk-importer'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" id="update_existing" name="update_existing" value="1" />
                                    <label for="update_existing"><?php _e('Update existing books if they already exist (matched by title)', 'book-bulk-importer'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="dry_run"><?php _e('Test Run', 'book-bulk-importer'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" id="dry_run" name="dry_run" value="1" />
                                    <label for="dry_run"><?php _e('Perform a test run (validate data without importing)', 'book-bulk-importer'); ?></label>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="button" id="validate-csv" class="button"><?php _e('Validate CSV', 'book-bulk-importer'); ?></button>
                            <button type="submit" id="import-books" class="button-primary" disabled><?php _e('Import Books', 'book-bulk-importer'); ?></button>
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
        $update_existing = isset($_POST['update_existing']) && $_POST['update_existing'] === '1';
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
            
            // Import books
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
            if ($format === 'legacy') {
                $csv_content = BookBulkImporter_CsvImporter::getLegacySampleCsvContent();
                $filename = 'sample-books-legacy.csv';
            } else {
                $csv_content = BookBulkImporter_CsvImporter::getSampleCsvContent();
                $filename = 'sample-books-indexed.csv';
            }
            
            wp_send_json_success(array(
                'content' => $csv_content,
                'filename' => $filename
            ));
            
        } catch (Exception $e) {
            wp_send_json_error('Failed to generate sample CSV: ' . $e->getMessage());
        }
    }
}