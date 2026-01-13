<?php 
    $category = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'object_ids' => get_posts([
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]),
    ]);


    $tag = get_terms([
        'taxonomy'   => 'post_tag',
        'hide_empty' => false,
        'object_ids' => get_posts([
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]),
    ]);
?>

<section id="news-detail" class="news-detail py-7 py-lg-9" aria-labelledby="news-title">
    <div class="container-xxl">
        <h1 id="news-title" class="jr-sec-title jr-sec-title-sub mb-5">News</h1>

        <div class="row g-5">
            <!-- Sidebar -->
            <?php get_template_part('template-parts/news/sidebar', 'news', (array('category'=> $category, 'tags'=> $tag))); ?>

            <!-- Main -->
            <div class="col-12 col-lg-9">
                <header class="news-head mb-3">
                    <div class="news-row-head">
                        <time class="news-date" datetime="2025-08-01"><?= get_the_date('Y.m.d'); ?></time>
                        <?php $categories = get_the_terms(get_the_ID(), 'category'); ?>
                        <!-- Curent category -->
                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <?php foreach ($categories as $cat) : ?>
                                <span class="news-chip"><?= $cat->name; ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <h2 class="news-title">
                        <?= get_the_title(); ?>
                    </h2>
                </header>

                <article class="news-body">
                    <!-- News Content -->
                    <?= the_content(); ?>
                    <!-- Url CPT -->
                     <?php 
                        $new_url = get_field('news_url');
                        if($new_url) :
                            ?>
                                <p><a class="news-link" href="<?= $new_url; ?>" target="_blank" rel="noopener">URL: <?= $new_url ?></a></p>
                            <?php
                        endif
                     ?>
                </article>

                <div class="news-tags mt-4">
                    <?php $tags = get_the_terms(get_the_ID(), 'post_tag'); ?>
                    <?php if ($tags && !is_wp_error($tags)) : ?>
                    <div class="mb-2 text-muted">Tag: </div>
                        <ul class="jr-tagcloud justify-content-start ms-0">
                            <?php foreach ($tags as $ctag) : ?>
                                <li><a href="<?= esc_url( home_url( '/news/' ) ); ?>?tag=<?php echo esc_attr($ctag->slug);?>">#<?= $ctag->name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="mt-5 text-center">
                    <a href="<?= esc_url( home_url( '/news/' ) ); ?>" class="btn btn-viewmore" id="btn-news-back" data-back="<?= esc_url( home_url( '/news/' ) ); ?>">
                        <span>Back to Index</span>
                        <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11.5" stroke="white" />
                            <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>