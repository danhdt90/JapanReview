<?php
    $query = $args['data'];
    $desktop_banner = $args['desktop_banner'];
    $mobile_banner = $args['mobile_banner'];
?>

<section id="jr-about" class="jr-about pb-0" aria-labelledby="issues-title">
    <div class="container">
        <h1 id="issues-title" class="jr-sec-title jr-sec-title-sub"><?= get_the_title(); ?></h1>
    </div>
    <figure class="about-hero__figure">
        <?php if($desktop_banner) : ?>
            <img src="<?= $desktop_banner['url']; ?>" alt="About artwork" class="about-hero__img d-none d-md-block">
        <?php endif; ?>
        <?php if($mobile_banner) : ?>
            <img src="<?= $mobile_banner['url']; ?>" alt="About artwork" class="about-hero__img d-block d-md-none">
        <?php endif; ?>
    </figure>
</section>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<section id="page-articles" class="page-articles" aria-labelledby="articles-title">
    <div class="container">
        <?php
            if ($query->have_posts()) :
                ?>
                    <!-- Grid -->
                    <div id="articles-grid" class="row g-3 g-lg-5 justify-content-start">
                        <!-- Item -->
                         <?php 
                            while ($query->have_posts()) : $query->the_post();
                                ?>
                                    <div class="col-6 col-sm-6 col-lg-4">
                                        <a href="<?= get_the_permalink(); ?>" class="issue-card">
                                            <div class="issue-cover ratio ratio-3x4">
                                                <img src="<?= get_field('cover_image')['url']; ?>" alt="<?= the_title(); ?>" loading="lazy">
                                            </div>
                                        </a>
                                    </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                         ?>
                    </div>
                    <!-- Pagination -->
                     <?php brandwoods_pagination($query); ?>
                <?php
            endif;
        ?>

    </div>
</section>