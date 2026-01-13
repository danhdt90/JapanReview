<?php
/**
 * The template for displaying taxonomy archive pages (Genre and Keywords)
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
$taxonomy = $queried_object->taxonomy;
$term = $queried_object;
$term_name = $term->name;
$term_description = $term->description;

// Determine taxonomy type for display
$taxonomy_label = '';
$taxonomy_icon = '';
if ($taxonomy === 'publication_type') {
    $taxonomy_label = __('Publication Type', 'brandwoods2025');
} elseif ($taxonomy === 'keywords_article') {
    $taxonomy_label = __('Keyword', 'brandwoods2025');
}
?>
<?php get_template_part('/template-parts/components/search', 'dual'); ?>
<section id="jr-about" class="jr-about pb-0" aria-labelledby="Issues-title">
    <div class="container">
        <h1 id="Issues-title" class="jr-sec-title-sub">#<?php echo esc_html($term_name); ?></h1>
    </div>
</section>
<section id="jr-articles" class="jr-articles" aria-labelledby="articles-title">
    <div class="container-xxl position-relative">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <?php if (have_posts()) : 
                    // Helper function to extract value from array or string
                    $extract_value = function($field) {
                        if (is_array($field)) {
                            return !empty($field[0]) ? $field[0] : '';
                        }
                        return $field;
                    };
                    
                    $delay = 100;
                    while (have_posts()) : the_post(); 
                        $articleDetail = [
                            'group_author' => pods_field('group_author'),
                            'volume' => pods_field('volume'),
                            'publication_date' => pods_field('publication_date'),
                            'start_page' => pods_field('start_page'),
                            'end_page' => pods_field('end_page'),
                        ];
                ?>
                    
                    <article class="art-item" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="row g-3 flex-nowrap">
                            <div class="col overflow-hidden">
                                <h3 class="art-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <ul class="art-meta">
                                    <?php 
                                    // Display authors
                                    if (!empty($articleDetail['group_author']) && is_array($articleDetail['group_author'])) {
                                        echo '<li>';
                                        foreach ($articleDetail['group_author'] as $index => $author) {
                                            echo esc_html($author);
                                            if ($index < count($articleDetail['group_author']) - 1) echo ', ';
                                        }
                                        echo '</li>';
                                    }
                                    
                                    // Display publication date
                                    $pub_date = $extract_value($articleDetail['publication_date']);
                                    if (!empty($pub_date)) {
                                        echo '<li>' . esc_html($pub_date) . '</li>';
                                    }
                                    
                                    // Display volume
                                    $volume = $extract_value($articleDetail['volume']);
                                    if (!empty($volume)) {
                                        echo '<li>' . esc_html($volume) . '巻</li>';
                                    }
                                    
                                    // Display pages
                                    $start_page = $extract_value($articleDetail['start_page']);
                                    $end_page = $extract_value($articleDetail['end_page']);
                                    if (!empty($start_page) || !empty($end_page)) {
                                        echo '<li>ページ：';
                                        if (!empty($start_page)) echo esc_html($start_page);
                                        if (!empty($start_page) && !empty($end_page)) echo '-';
                                        if (!empty($end_page)) echo esc_html($end_page);
                                        echo '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </article>
                    
                <?php 
                    $delay = ($delay >= 300) ? 100 : $delay + 100;
                    endwhile; 
                ?>

                <!-- Pagination -->
                <?php 
                global $wp_query;
                brandwoods_pagination($wp_query); 
                ?>

                <?php else : ?>

                <!-- No Articles Found -->
                <div class="no-articles-found text-center py-5" data-aos="fade-up">
                    <h2><?php _e('No Articles Found', 'brandwoods2025'); ?></h2>
                    <p class="mb-4">
                        <?php 
                        printf(
                            __('Sorry, no articles were found for %s "%s".', 'brandwoods2025'),
                            strtolower($taxonomy_label),
                            esc_html($term_name)
                        ); 
                        ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/articles')); ?>" class="btn btn-viewmore mt-4">
                        <span>Browse All Articles</span>
                        <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11.5" stroke="white" />
                            <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                        </svg>
                    </a>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Search Component -->
<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<?php
get_footer();
?>
