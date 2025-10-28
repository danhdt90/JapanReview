<?php
/**
 * The template for displaying single book posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
            <?php 
                $vals_array = pods_field( 'other_title' ); 
                
                $vals_string = pods_field_display( 'other_title' ); 
                
                echo '<h3>Output display:</h3>';
               foreach ( $vals_array as $index=> $val ) {
                   echo 'の他のタイトル['.$index.']の他のタイトル:'.$val . '<br>';
               }
            ?>
    </main>
</div>