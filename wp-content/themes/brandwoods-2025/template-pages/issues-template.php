<?php 
/*
    Template Name: Issuse Page
*/

$article_posts = get_posts([
    'post_type'      => 'article',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$article_volumes = [];

foreach ($article_posts as $id) {
    $vol = get_post_meta($id, 'volume', true);

    if ($vol !== '' && $vol !== null) {
        $article_volumes[] = $vol;
    }
}

$article_volumes = array_unique($article_volumes);

$desktop_banner = get_field('desktop_banner');
$mobile_banner = get_field('mobile_banner');

$paged = get_query_var('paged') ?: (get_query_var('page') ?: 1);

$args = [
    'post_type'      => 'issue',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'modified', // or date
    'order'          => 'DESC',

    'meta_query' => [
        'relation' => 'AND',

        // Volume not empty
        [
            'key'     => 'volume',
            'value'   => '',
            'compare' => '!=',
        ],

        // Volume article = Volume Issuse
        [
            'key'     => 'volume',
            'value'   => $article_volumes,
            'compare' => 'IN',
        ],
    ],

    'no_found_rows'          => false,
    'cache_results'          => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,
];

$query = new WP_Query($args);

?>

<?php get_header(); ?>
    <?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>
    <?php get_template_part('/template-parts/issue/archive', 'issue', (array('desktop_banner'=> $desktop_banner , 'mobile_banner'=> $mobile_banner , 'data' => $query))); ?>
<?php get_footer(); ?>