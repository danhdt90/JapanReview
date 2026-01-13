<?php 
/*
    Template Name: 404 Page
*/
?>

<?php get_header(); ?>
<?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>

<section class="not-found" aria-labelledby="not-found-title">
    <div class="container-xxl">
        <h1 id="not-found-title" class="jr-sec-title text-center mb-5"><?= get_the_title(); ?></h1>

        <div class="row g-5">
            <div class="col-12 text-center">
                <?= the_content(); ?>
            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>