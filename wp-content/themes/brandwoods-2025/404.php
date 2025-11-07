<?php
    $page_404 = get_page_by_path('page-404');
    $title = '404';
    $image_url = IMAGE_PATH . "/404.jpg";

    if ($page_404) {
        $page_id = $page_404->ID;
        $title = get_the_title($page_id);
        $image_url = get_the_post_thumbnail_url($page_id, 'full');
    }

    get_header();
    get_template_part('/template-parts/components/breadcrumb', null , array('title' => $title ));

    ?>        
        <section id="news-detail" class="news-detail py-7 py-lg-9" aria-labelledby="news-title">
            <div class="container-xxl">
                <h1 id="news-title" class="jr-sec-title text-center mb-5"><?= $title ?></h1>

                <div class="row g-5">
                    <div class="col-12 text-center">
                        <p><img class="img-fluid" src="<?= $image_url; ?>" alt=""></p>
                    </div>
                </div>
            </div>
        </section>
    <?php

    get_template_part('/template-parts/components/search', 'dual');
    get_template_part('/template-parts/components/contact', 'form');
    get_footer();
?>
