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

<?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => 'Issuse')) ?>

<section id="page-articles" class="page-articles py-7 py-lg-9" aria-labelledby="articles-title">
    <div class="container">
        <h1 id="articles-title" class="jr-sec-title text-center">発行年 2024年など</h1>
        <?php
            if ($query->have_posts()) :
                ?>
                    <!-- Grid -->
                    <div id="articles-grid" class="row g-5 justify-content-center">
                        <!-- Item -->
                         <?php 
                            while ($query->have_posts()) : $query->the_post();
                                ?>
                                    <div class="col-10 col-sm-6 col-lg-4">
                                        <a href="<?= get_the_permalink(); ?>" class="issue-card">
                                            <div class="issue-cover ratio ratio-3x4">
                                                <img src="<?= get_field('cover_image')['url']; ?>" alt="<?= the_title(); ?>" loading="lazy">
                                            </div>
                                        </a>
                                    </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                         ?>
                    </div>
                    <!-- Pagination -->
                     <?php brandwoods_pagination($query); ?>
                <?php
            endif;
        ?>

        

    </div>
</section>
