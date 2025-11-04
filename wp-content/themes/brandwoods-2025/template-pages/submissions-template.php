<?php 
/*
    Template Name: Submissions Page
*/
?>

<?php get_header(); ?>

    <?php get_template_part('/template-parts/components/breadcrumb', null , array('id' => get_the_ID())) ?>

    <section id="jr-about" class="jr-about py-7 py-lg-9" aria-labelledby="about-title">
        <div class="container">
            <!-- Title -->
            <h1 id="about-title" class="jr-sec-title jr-sec-title-sub text-center mb-5"><?= get_the_title(); ?></h1>
            <!-- Content -->
           <?php the_content() ?>
        </div>
    </section>
    
    <?php get_template_part('/template-parts/components/search', 'dual'); ?>
    <?php get_template_part('/template-parts/components/contact', 'form'); ?>

<?php get_footer(); ?>