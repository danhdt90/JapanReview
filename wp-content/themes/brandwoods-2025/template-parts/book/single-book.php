<?php
/**
 * The template for displaying single book posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BrandWoods_2025
 * @since BrandWoods 2025 1.0
 */

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <!-- Search Component Demo -->
        <div class="book-page-search">
            <h2><?php _e('Search Books', 'brandwoods-2025'); ?></h2>
            
            <?php
            // Load search component for books
            get_template_part('template-parts/components/search-form', null, array(
                'placeholder'   => __('Search books by title, author, or ISBN...', 'brandwoods-2025'),
                'button_text'   => __('Find Books', 'brandwoods-2025'),
                'search_type'   => 'book1',
                'show_filters'  => true,
                'form_class'    => 'book-search-form',
            ));
            ?>
        </div>

        <!-- Original Content (for comparison) -->
        <div class="original-content" style="margin-top: 3rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
            <h3>Original Output:</h3>
            <?php 
                $vals_array = pods_field( 'other_title' ); 
                
                $vals_string = pods_field_display( 'other_title' ); 
                
                echo '<h4>Output display:</h4>';
               if (is_array($vals_array)) {
                   foreach ( $vals_array as $index=> $val ) {
                       echo 'の他のタイトル['.$index.']の他のタイトル:'.$val . '<br>';
                   }
               }
            ?>
        </div>
        
    </main>
</div>

<style>
.book-page-search {
    margin-bottom: 2rem;
}

.book-page-search h2 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.5rem;
}

.original-content {
    border-left: 4px solid #6b7280;
}

.original-content h3,
.original-content h4 {
    color: #374151;
    margin-bottom: 0.75rem;
}
</style>