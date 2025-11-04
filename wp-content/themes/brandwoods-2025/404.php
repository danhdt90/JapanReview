<?php
    get_header();
    get_template_part('/template-parts/components/breadcrumb', null , array('id' => $page_id));

    $page_404 = get_page_by_path('page-404');
    $page_id = $page_404->ID;

    if ($page_404) {
        ?>        
            <section id="news-detail" class="news-detail py-7 py-lg-9" aria-labelledby="news-title">
                <div class="container-xxl">
                    <h1 id="news-title" class="jr-sec-title text-center mb-5"><?= get_the_title($page_id); ?></h1>

                    <div class="row g-5">
                        <div class="col-12 text-center">
                            <p><img class="img-fluid" src="<?= get_the_post_thumbnail_url($page_id, 'full'); ?>" alt=""></p>
                        </div>
                    </div>
                </div>
            </section>

        <?php
    } else {
        ?>
            <section id="news-detail" class="news-detail py-7 py-lg-9" aria-labelledby="news-title">
                <div class="container-xxl">
                    <h1 id="news-title" class="jr-sec-title text-center mb-5">404</h1>

                    <div class="row g-5">
                        <div class="col-12 text-center">
                            <p><img class="img-fluid" src="<?= IMAGE_PATH; ?>/404.jpg" alt=""></p>
                        </div>
                    </div>
                </div>
            </section>

        <?php
    }

    get_template_part('/template-parts/components/search', 'dual');
    get_template_part('/template-parts/components/contact', 'form');
    get_footer();
?>
