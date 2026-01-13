<?php
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

    global $wp_query;

?>

<?php get_template_part('/template-parts/news/archive', 'news', (array('categories'=> $news_categories , 'tags'=> $news_tags , 'news_data' => $wp_query))); ?>
