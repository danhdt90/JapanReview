<?php
    $category = $args['categories'];
    $tag = $args['tags'];
    $news = $args['news_data'];
?>
<section id="page-news" class="page-news py-7 py-lg-9" aria-labelledby="news-title">
    <div class="container-xxl">
        <h1 id="news-title" class="jr-sec-title jr-sec-title-sub mb-5">News</h1>

        <div class="row g-5">
            <!-- SIDEBAR -->
            <?php get_template_part('template-parts/news/sidebar', 'news', (array('category'=> $category, 'tags'=> $tag))); ?>

            <!-- LIST -->
            <?php
                if ($news->have_posts()) :
                    echo '<div class="col-12 col-lg-9"> <div id="news-list">';
                        while ($news->have_posts()) : $news->the_post();
                            $categories = get_the_terms(get_the_ID(), 'category');
                            $post_tag = get_the_terms(get_the_ID(), 'post_tag');
                            ?>
                                <article class="news-row" data-cat="Announcement" data-tags="Japan,Publication">
                                    <div class="news-row-head">
                                        <time class="news-date" datetime="<?= get_the_date('Y.m.d'); ?>"><?= get_the_date('Y.m.d'); ?></time>
                                        <!-- Curent category -->
                                        <?php if ($categories && !is_wp_error($categories)) : ?>
                                            <?php foreach ($categories as $cat) : ?>
                                                <span class="news-chip"><?= $cat->name; ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="news-row-title">
                                        <a href="<?= get_the_permalink(); ?>"><?= the_title(); ?></a>
                                    </h3>
                                    <ul class="news-row-tags">
                                        <?php if ($post_tag && !is_wp_error($post_tag)) : ?>
                                            <?php foreach ($post_tag as $ctag) : ?>
                                                <li><a href="<?= esc_url( home_url( '/news/' ) ); ?>?tag=<?php echo esc_attr($ctag->slug); ?>">#<?= $ctag->name; ?></a></li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <hr class="news-hr">
                                </article>
                            <?php
                        endwhile;
                    echo '</div></div>';
                    wp_reset_postdata();
                    
                    // Paginate News
                    brandwoods_pagination($news);
                    
                endif;
            ?>
        </div>
    </div>
</section>