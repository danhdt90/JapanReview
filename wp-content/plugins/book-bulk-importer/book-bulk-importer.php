<?php
/**
 * Plugin Name: Article Bulk Importer
 * Description: Import articles in bulk from CSV files with Pods repeatable fields support
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BOOK_BULK_IMPORTER_VERSION', '1.0.0');
define('BOOK_BULK_IMPORTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOOK_BULK_IMPORTER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class BookBulkImporter {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Hook into WordPress
        add_action('init', array($this, 'loadDependencies'));
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin dependencies
     */
    public function loadDependencies() {
        // Check if Pods is active
        if (!class_exists('Pods')) {
            add_action('admin_notices', array($this, 'podsNotActiveNotice'));
            return;
        }
        
        // Include required files
        require_once BOOK_BULK_IMPORTER_PLUGIN_DIR . 'includes/class-admin-page.php';
        require_once BOOK_BULK_IMPORTER_PLUGIN_DIR . 'includes/class-csv-importer.php';
        require_once BOOK_BULK_IMPORTER_PLUGIN_DIR . 'includes/class-book-importer.php';
        
        // Initialize classes
        new BookBulkImporter_AdminPage();
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_submenu_page(
            'tools.php',
            'Article Bulk Importer',
            'Article Bulk Importer',
            'manage_options',
            'book-bulk-importer',
            array($this, 'adminPageCallback')
        );
    }
    
    /**
     * Admin page callback
     */
    public function adminPageCallback() {
        if (class_exists('BookBulkImporter_AdminPage')) {
            $adminPage = new BookBulkImporter_AdminPage();
            $adminPage->render();
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueueAdminScripts($hook) {
        if ($hook !== 'tools_page_book-bulk-importer') {
            return;
        }
        
        wp_enqueue_script(
            'book-bulk-importer-admin',
            BOOK_BULK_IMPORTER_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            BOOK_BULK_IMPORTER_VERSION,
            true
        );
        
        wp_enqueue_style(
            'book-bulk-importer-admin',
            BOOK_BULK_IMPORTER_PLUGIN_URL . 'assets/admin.css',
            array(),
            BOOK_BULK_IMPORTER_VERSION
        );
        
        // Localize script for AJAX
        wp_localize_script('book-bulk-importer-admin', 'bookBulkImporter', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('book_bulk_importer_nonce'),
            'strings' => array(
                'importing' => __('Importing...', 'book-bulk-importer'),
                'success' => __('Import completed successfully!', 'book-bulk-importer'),
                'error' => __('Import failed. Please try again.', 'book-bulk-importer'),
            )
        ));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Check if Pods is active
        if (!class_exists('Pods')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('Article Bulk Importer requires Pods plugin to be active.');
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $article_import_dir = $upload_dir['basedir'] . '/article-imports';
        if (!file_exists($article_import_dir)) {
            wp_mkdir_p($article_import_dir);
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up temporary files if needed
    }
    
    /**
     * Notice for Pods not active
     */
    public function podsNotActiveNotice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Article Bulk Importer requires Pods plugin to be active.', 'book-bulk-importer'); ?></p>
        </div>
        <?php
    }
}

// Initialize plugin
add_action('plugins_loaded', function() {
    BookBulkImporter::getInstance();
});