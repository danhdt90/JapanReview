<?php 
/*
    Template Name: About Page
*/
?>

<?php get_header(); ?>
<?php get_template_part('/template-parts/components/breadcrumb', null , array('title' => get_the_title())) ?>

    <section id="jr-about" class="jr-about py-7 py-lg-9" aria-labelledby="about-title">
        <div class="container">
            <!-- Title -->
            <h1 id="about-title" class="jr-sec-title text-center mb-5 jr-sec-title-sub"><?= get_the_title(); ?></h1>

            <!-- Content -->
            <?= the_content(); ?>

            <!-- Related Links -->
            <section class="jr-about-section mb-3">
                <h2 class="jr-about-heading text-center mb-4">Related Links</h2>
                <ul class="jr-related-links list-unstyled text-center">
                    <li><a href="<?= get_field('related_links_1'); ?>" target="_blank">URL: <?= get_field('related_links_1'); ?></a></li>
                    <li><a href="<?= get_field('related_links_2'); ?>" target="_blank">URL: <?= get_field('related_links_2'); ?></a></li>
                    <li><a href="<?= get_field('related_links_3'); ?>" target="_blank">URL: <?= get_field('related_links_3'); ?></a></li>
                    <li><a href="<?= get_field('related_links_4'); ?>" target="_blank">URL: <?= get_field('related_links_4'); ?></a></li>
                </ul>
            </section>

        </div>
    </section>
    <?php get_template_part('/template-parts/components/search', 'dual'); ?>
    <?php get_template_part('/template-parts/components/contact', 'form'); ?>
<?php get_footer(); ?>