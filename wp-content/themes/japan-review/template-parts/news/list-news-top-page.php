<?php 
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'modified', // or date
        'order'          => 'DESC',

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

                <h2 id="news-title" class="jr-sec-title text-center">News</h2>

                <div class="row justify-content-center jr-news-list">
                    <div class="col-12 col-lg-6">
                        <!-- 1 item -->
                        <?php 
                            while ($query->have_posts()) : $query->the_post();
                            $categories = get_the_terms(get_the_ID(), 'category');
                            $post_tag = get_the_terms(get_the_ID(), 'post_tag');
                                ?>
                                    <article class="news-item">
                                        <div class="news-meta">
                                            <time datetime="2025-10-01"><?= get_the_date('Y.m.d'); ?></time>
                                            <?php if ($categories && !is_wp_error($categories)) : ?>
                                                <?php foreach ($categories as $cat) : ?>
                                                    <a class="news-cat" href="<?= esc_url( home_url( '/news/' ) ); ?>?category=<?php echo esc_attr($cat->slug); ?>"><?= $cat->name; ?></a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="news-title">
                                            <a href="<?= get_the_permalink(); ?>"><?= the_title(); ?></a>
                                        </h3>

                                        <ul class="news-tags">
                                            <?php if ($post_tag && !is_wp_error($post_tag)) : ?>
                                                <?php foreach ($post_tag as $ctag) : ?>
                                                    <li><a href="<?= esc_url( home_url( '/news/' ) ); ?>?tag=<?php echo esc_attr($ctag->slug); ?>">#<?= $ctag->name; ?></a></li>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>

                                        <hr class="news-divider">
                                    </article>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        ?>
                        <!-- View more -->
                        <div class="text-center mt-4">
                            <a href="<?= esc_url( home_url( '/news/' ) ); ?>" class="btn btn-viewmore">
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
                <img class="jr-news-ill jr-news-ill-left" src="<?= IMAGE_PATH; ?>/illust_people_left.png" alt="" aria-hidden="true">
                <img class="jr-news-ill jr-news-ill-right" src="<?= IMAGE_PATH; ?>/illust_people_right.png" alt="" aria-hidden="true">
                <img class="jr-news-ill jr-news-ill-top" src="<?= IMAGE_PATH; ?>/illust_people_top.png" alt="" aria-hidden="true">
                <img class="illust-people-sp" src="<?= IMAGE_PATH; ?>/illust_people_sp.png" alt="" aria-hidden="true">
            </div>
        <?php
    endif;
?>