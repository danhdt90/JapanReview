<?php
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = [
        'post_type'      => 'issue',
        'posts_per_page' => 3,
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
            <div class="container text-center">
                <h2 id="issues-title" class="jr-sec-title" data-aos="fade-up">Latest Issues</h2>

                <div class="row g-5 justify-content-center">
                <!-- Issue 1 -->
                <?php 
                    while ($query->have_posts()) : $query->the_post();
                        ?>
                            <div class="col-10 col-sm-6 col-lg-4">
                                <a class="issue-card" href="<?= get_the_permalink(); ?>" data-aos="fade-up" data-aos-delay="100">
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
                    <a href="<?= esc_url( home_url( '/issues/' ) ); ?>" class="btn btn-viewmore" data-aos="fade-up" data-aos-delay="400">
                        <span>View More</span>
                        <span class="btn-circle" aria-hidden="true">
                        <i class="bi bi-arrow-right-short"></i>
                        </span>
                    </a>
                </div>
            </div>
        <?php
    endif;
?>