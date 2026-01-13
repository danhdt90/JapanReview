<?php
    $page_404 = get_page_by_path('page-404');
    $title = '404 NOT FOUND';
    $image_url = IMAGE_PATH . "/404.jpg";
    $content = '<p class="mb-4">Page not found.<br>
                    The page you’re looking for may have been moved or no longer exists.<br>
                    Return to the <a href="/">homepage.</a></p>';

    if ($page_404) {
        $page_id = $page_404->ID;
        $title = get_the_title($page_id);
        $image_url = get_the_post_thumbnail_url($page_id, 'full');
        $content_raw = get_post_field('post_content', $page_id);
        $content = apply_filters('the_content', $content_raw);
    }

    get_header();
    get_template_part('/template-parts/components/breadcrumb', null , array('title' => $title ));

    ?>        
        <section class="not-found" aria-labelledby="not-found-title">
            <div class="container-xxl">
                <h1 id="not-found-title" class="jr-sec-title text-center mb-5"><?= $title; ?></h1>

                <div class="row g-5">
                    <div class="col-12 text-center">
                        <?= $content; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php

    get_footer();
?>
