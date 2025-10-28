<?php get_header(); ?>

<?php 
    $post_type = get_post_type();

    get_template_part('template-parts/'. $post_type .'/single', $post_type);
 ?>
    
<?php get_footer(); ?>