<?php get_header(); ?>
<?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>
<?php 
    $post_type = get_post_type();

    if($post_type == 'post') {
        get_template_part('template-parts/news/single', 'news');
    } else {
        get_template_part('template-parts/'. $post_type .'/single', $post_type);
    }
    
 ?>
    
<?php get_footer(); ?>