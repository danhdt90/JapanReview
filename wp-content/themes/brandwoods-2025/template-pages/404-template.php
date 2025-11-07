<?php 
/*
    Template Name: 404 Page
*/
?>

<?php get_header(); ?>
<?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>

<section id="news-detail" class="news-detail py-7 py-lg-9" aria-labelledby="news-title">
    <div class="container-xxl">
        <h1 id="news-title" class="jr-sec-title text-center mb-5"><?= get_the_title(); ?></h1>

        <div class="row g-5">
            <div class="col-12 text-center">
                <p><img class="img-fluid" src="<?= get_the_post_thumbnail_url(); ?>" alt=""></p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>