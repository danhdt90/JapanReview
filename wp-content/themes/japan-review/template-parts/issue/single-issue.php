<?php
    $cover_image = get_field('cover_image');
    $isuse_vol = get_field('volume');

    $paged = get_query_var('paged') ?: (get_query_var('page') ?: 1);

    $args = [
        'post_type'      => 'article',
        'posts_per_page' => 5,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'modified', // or date
        'order'          => 'DESC',

        'meta_query' => [
            [
                'key'     => 'volume',
                'value'   => $isuse_vol,   
                'compare' => 'IN'
            ]
        ],

        'no_found_rows'          => false,
        'cache_results'          => true,
        'update_post_term_cache' => true,
        'update_post_meta_cache' => true,
    ];

    $query = new WP_Query($args);
?>

<section id="jr-about" class="jr-about pb-0" aria-labelledby="Issues-title">
    <div class="container">
        <h1 id="Issues-title" class="jr-sec-title jr-sec-title-sub"><?= get_the_title(); ?></h1>
    </div>

    <!-- figure ra ngoài container -->
    <figure class="banner-figure">
        <img src="<?= $cover_image['url'] ?>" alt="Issues artwork" class="img-fluid js-banner-img">
    </figure>
</section>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<?php
    if ($query->have_posts()) : 
        ?>
            <section id="jr-articles" class="jr-articles" aria-labelledby="articles-title">
                <div class="container-xxl position-relative">
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
                                    <article class="art-item" data-aos="fade-up" data-aos-delay="100">
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

                            <!-- Pagination -->
                            <?php brandwoods_pagination($query); ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    endif;
?>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>