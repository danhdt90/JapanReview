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
        <h1 id="news-title" class="jr-sec-title jr-sec-title-sub text-center mb-5">News</h1>

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

                <hr class="news-hr">

                <article class="news-body">
                    <!-- News Content -->
                    <?= the_content(); ?>
                    <!-- Url CPT -->
                     <?php $new_url = get_field('news_url'); ?>
                    <p>URL: <a class="news-link" href="<?= $new_url; ?>" target="_blank" rel="noopener"><?= $new_url ?></a></p>
                </article>

                <div class="news-tags mt-4">
                    <?php $tags = get_the_terms(get_the_ID(), 'post_tag'); ?>
                    <?php if ($tags && !is_wp_error($tags)) : ?>
                    <div class="mb-2 fw-medium text-muted">タグ登録：</div>
                        <ul class="jr-tagcloud justify-content-start ms-0">
                            <?php foreach ($tags as $ctag) : ?>
                                <li><a href="<?= esc_url( home_url( '/news/' ) ); ?>?tag=<?php echo esc_attr($ctag->slug);?>">#<?= $ctag->name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="mt-5 text-center">
                    <a href="<?= esc_url( home_url( '/news/' ) ); ?>" class="btn btn-viewmore black" id="btn-news-back">
                        <span>Back to Index</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>