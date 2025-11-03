<?php 
/*
    Template Name: News Page
*/

    $news_categories = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'object_ids' => get_posts([
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]),
    ]);


    $news_tags = get_terms([
        'taxonomy'   => 'post_tag',
        'hide_empty' => false,
        'object_ids' => get_posts([
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]),
    ]);

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ( isset($_GET['category']) && $_GET['category'] != '' ) {
        $args['tax_query'][] = [
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['category']),
        ];
    }

    if ( isset($_GET['tag']) && $_GET['tag'] != '' ) {
        $args['tax_query'][] = [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['tag']),
        ];
    }

    $query = new WP_Query($args);
    
?>

<?php get_header(); ?>
<?php get_template_part('/template-parts/components/breadcrumb', null , array('id' => get_the_ID())) ?>
    <?php get_template_part('template-parts/news/archive', 'news', (array('categories'=> $news_categories , 'tags'=> $news_tags , 'news_data' => $query))); ?>
<?php get_footer(); ?>