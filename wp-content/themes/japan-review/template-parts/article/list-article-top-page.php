<?php

    $issuse_posts = get_posts([
        'post_type'      => 'issue',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $iss_volumes = [];

    foreach ($issuse_posts as $id) {
        $vol = get_post_meta($id, 'volume', true);
    
        if ($vol !== '' && $vol !== null) {
            $iss_volumes[] = $vol;
        }
    }
    
    $iss_volumes = array_unique($iss_volumes);

    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = [
        'post_type'      => 'article',
        'posts_per_page' => 5,
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
                'value'   => $iss_volumes,
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
            <div class="container-xxl position-relative">

                <h2 id="articles-title" class="jr-sec-title text-center">Latest Articles</h2>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9">

                        <!-- 1 item -->
                        <?php 
                            while ($query->have_posts()) : $query->the_post();

                            $publication_date = pods_field('publication_date');
                            $volume = pods_field('volume');
                            $start_page = pods_field('start_page');
                            $end_page = pods_field('end_page');

                            $extract_value = function($field) {
                                if (is_array($field)) {
                                    return !empty($field[0]) ? $field[0] : '';
                                }
                                return $field;
                            };

                            $date_value = $extract_value($publication_date);
                            $volume_value = $extract_value($volume);
                            $start_page_value = $extract_value($start_page);
                            $end_page_value = $extract_value($end_page);
                        ?>
                            <article class="art-item">
                                <div class="row g-3 flex-nowrap">
                                    <!-- text -->
                                    <div class="col overflow-hidden">
                                        <h3 class="art-title">
                                            <a href="<?= get_the_permalink(); ?>"><?= get_the_title(); ?></a>
                                        </h3>
                                        <ul class="art-meta">
                                            <li>
                                                <?php $group_authors = pods_field('group_author'); ?>
                                                <?php if (!empty($group_authors) && is_array($group_authors)): ?>
                                                    <?php foreach ($group_authors as $index => $author): ?>
                                                        <?php echo esc_html($author); ?>
                                                        <?php if ($index < count($group_authors) - 1) echo ', '; ?>
                                                    <?php endforeach; ?>    
                                                <?php endif; ?>
                                            </li>
                                            <?php if (!empty($date_value)): ?>
                                                <li><?php echo esc_html($date_value); ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($volume_value)): ?>
                                                <li><?php echo esc_html($volume_value); ?>巻</li>
                                            <?php endif; ?>
                                            <li>
                                                ページ：<?php if (!empty($start_page_value)): echo esc_html($start_page_value); endif; ?><?php if (!empty($start_page_value) && !empty($end_page_value)): echo '-'; endif; ?><?php if (!empty($end_page_value)): echo esc_html($end_page_value); endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </article>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        ?>
                        <!-- View more -->
                        <div class="text-center mt-3">
                            <a href="<?= esc_url( home_url( '/articles/' ) ); ?>" class="btn btn-viewmore">
                                <span>View More</span>
                                <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11.5" stroke="white" />
                                <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Illustrations -->
                <img class="jr-art-ill jr-art-ill-left" src="<?= IMAGE_PATH; ?>/illust_people_left2.png" alt="" aria-hidden="true">
                <img class="jr-art-ill jr-art-ill-right" src="<?= IMAGE_PATH; ?>/illust_people_right2.png" alt="" aria-hidden="true">
                <img class="jr-art-ill jr-art-ill-right-top" src="<?= IMAGE_PATH; ?>/illust_people_right_top.png" alt="" aria-hidden="true">
                <img class="jr-art-ill jr-art-ill-left-mid" src="<?= IMAGE_PATH; ?>/illust_people_left-mid.png" alt="" aria-hidden="true">

                <img class="illust-people-sp" src="<?= IMAGE_PATH; ?>/illust_people_sp2.png" alt="" aria-hidden="true">
            </div>
        <?php
    endif;
?>