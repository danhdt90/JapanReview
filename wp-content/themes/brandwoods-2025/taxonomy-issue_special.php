<?php
/**
 * The template for displaying Special Issue taxonomy archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#taxonomy
 *
 * @package WordPress
 * @subpackage Brandwoods_2025
 * @since Brandwoods 2025 1.0
 */

get_header();

// Get current taxonomy and term
$queried_object = get_queried_object();
$term = $queried_object;
$term_name = $term->name;
$term_description = $term->description;

// Setup query args
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$query_args = array(
    'post_type' => 'issue',
    'posts_per_page' => 12,
    'paged' => $paged,
    'tax_query' => array(
        array(
            'taxonomy' => 'issue_special',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
);

$issue_query = new WP_Query($query_args);
?>

<section id="jr-about" class="jr-about pb-0" aria-labelledby="issues-title">
    <div class="container">
        <h1 id="issues-title" class="jr-sec-title jr-sec-title-sub"><?php echo esc_html($term_name); ?></h1>
    </div>
    <?php if (!empty($term_description)) : ?>
        <figure class="about-hero__figure">
            <div class="taxonomy-description container py-4">
                <?php echo wpautop(wp_kses_post($term_description)); ?>
            </div>
        </figure>
    <?php endif; ?>
</section>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<section id="page-articles" class="page-articles" aria-labelledby="articles-title">
    <div class="container">
        <?php if ($issue_query->have_posts()) : ?>
            
            <!-- Grid -->
            <div id="articles-grid" class="row g-3 g-lg-5 justify-content-start">
                <!-- Item -->
                <?php 
                while ($issue_query->have_posts()) : $issue_query->the_post();
                    $cover_image = get_field('cover_image');
                ?>
                    <div class="col-6 col-sm-6 col-lg-4">
                        <a href="<?= get_the_permalink(); ?>" class="issue-card">
                            <div class="issue-cover ratio ratio-3x4">
                                <?php if ($cover_image && isset($cover_image['url'])) : ?>
                                    <img src="<?= esc_url($cover_image['url']); ?>" alt="<?= the_title(); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="placeholder-cover d-flex align-items-center justify-content-center bg-light">
                                        <span><?php the_title(); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php 
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            
            <!-- Pagination -->
            <?php 
            // Use custom pagination function if available
            if (function_exists('brandwoods_pagination')) {
                brandwoods_pagination($issue_query);
            } else {
                // Fallback pagination
                echo paginate_links(array(
                    'total' => $issue_query->max_num_pages,
                    'current' => $paged,
                    'mid_size' => 2,
                    'prev_text' => __('← Previous', 'brandwoods2025'),
                    'next_text' => __('Next →', 'brandwoods2025'),
                ));
            }
            ?>

        <?php endif; ?>

    </div>
</section>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<?php
get_footer();
?>
