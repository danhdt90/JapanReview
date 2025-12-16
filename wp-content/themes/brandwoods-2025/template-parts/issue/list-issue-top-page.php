<?php

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

    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = [
        'post_type'      => 'issue',
        'posts_per_page' => 3,
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

<?php
    if ($query->have_posts()) : 
        ?>
            <div class="container text-center">
                <h2 id="issues-title" class="jr-sec-title">Latest Issues</h2>

                <div class="row g-5 justify-content-center">
                <!-- Issue 1 -->
                <?php 
                    while ($query->have_posts()) : $query->the_post();
                        ?>
                            <div class="col-10 col-sm-6 col-lg-4">
                                <a class="issue-card" href="<?= get_the_permalink(); ?>">
                                    <figure class="m-0">
                                        <div class="issue-cover ratio ratio-3x4">
                                            <img src="<?= get_field('cover_image')['url']; ?>" class="img-fluid" alt="<?= the_title(); ?>" loading="lazy">
                                        </div>
                                    </figure>
                                </a>
                            </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                ?>
                <!-- View more -->
                <div class="mt-5">
                    <a href="<?= esc_url( home_url( '/issues/' ) ); ?>" class="btn btn-viewmore">
                        <span>View More</span>
                        <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11.5" stroke="white" />
                            <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php
    endif;
?>