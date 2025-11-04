<?php 
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
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

                <h2 id="news-title" class="jr-sec-title text-center" data-aos="fade-up">News</h2>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-6">
                        <!-- 1 item -->
                        <?php 
                            while ($query->have_posts()) : $query->the_post();
                            $categories = get_the_terms(get_the_ID(), 'category');
                            $post_tag = get_the_terms(get_the_ID(), 'post_tag');
                                ?>
                                    <article class="news-item" data-aos="fade-up" data-aos-delay="100">
                                        <div class="news-meta">
                                            <time datetime="2025-10-01"><?= get_the_date('Y.m.d'); ?></time>
                                            <?php if ($categories && !is_wp_error($categories)) : ?>
                                                <?php foreach ($categories as $cat) : ?>
                                                    <a class="news-cat" href="/news?category=<?php echo esc_attr($cat->slug); ?>"><?= $cat->name; ?></a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="news-title">
                                            <a href="<?= get_the_permalink(); ?>"><?= the_title(); ?></a>
                                        </h3>

                                        <ul class="news-tags">
                                            <?php if ($post_tag && !is_wp_error($post_tag)) : ?>
                                                <?php foreach ($post_tag as $ctag) : ?>
                                                    <li><a href="/news?tag=<?php echo esc_attr($ctag->slug); ?>">#<?= $ctag->name; ?></a></li>
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
                        <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="500">
                            <a href="/news" class="btn btn-viewmore">
                                <span>View More</span>
                                <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Illustrations -->
                <img class="jr-news-ill jr-news-ill-left" src="<?= IMAGE_PATH; ?>/illust_people_left.png" alt="" aria-hidden="true">
                <img class="jr-news-ill jr-news-ill-right" src="<?= IMAGE_PATH; ?>/illust_people_right.png" alt="" aria-hidden="true">

            </div>
        <?php
    else :
        echo '<p>No news found.</p>';
    endif;
?>