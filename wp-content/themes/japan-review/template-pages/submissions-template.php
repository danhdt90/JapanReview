<?php 
/*
    Template Name: Submissions Page
*/
$desktop_banner = get_field('desktop_banner');
$mobile_banner = get_field('mobile_banner');
?>

<?php get_header(); ?>

    <?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>

    <section id="jr-about" class="jr-about" aria-labelledby="Submissions-title">
        <div class="container">
            <h1 id="Submissions-title" class="jr-sec-title jr-sec-title-sub">Submissions</h1>
        </div>

        <!-- figure ra ngoài container -->
        <figure class="about-hero__figure">
            <?php if($desktop_banner) : ?>
                <img src="<?= $desktop_banner['url']; ?>" alt="About artwork" class="about-hero__img d-none d-md-block">
            <?php endif; ?>
            <?php if($mobile_banner) : ?>
                <img src="<?= $mobile_banner['url']; ?>" alt="About artwork" class="about-hero__img d-block d-md-none">
            <?php endif; ?>
        </figure>

        <div class="container">
            <!-- editor content -->
            <?= the_content(); ?>
        </div>
    </section>

<?php get_footer(); ?>