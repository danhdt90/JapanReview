<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Brandwoods_2025
 * @since Brandwoods 2025 1.0
 */

get_header();

$search_query = brandwoods_get_search_term(); // Use custom function

// Helper function to extract value from array or string
$extract_value = function($field) {
    if (is_array($field)) {
        return !empty($field[0]) ? $field[0] : '';
    }
    return $field;
};
?>

<section id="jr-about" class="jr-about pb-0" aria-labelledby="search-title">
    <div class="container">
        <h1 id="search-title" class="jr-sec-title jr-sec-title-sub">
            <?php if (!empty($search_query)) : ?>
                Search Results for: "<?php echo esc_html($search_query); ?>"
            <?php else : ?>
                Search Results
            <?php endif; ?>
        </h1>
    </div>
</section>

<?php if (have_posts()) : ?>
    <section id="jr-articles" class="jr-articles" aria-labelledby="articles-title">
        <div class="container-xxl position-relative">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">
                    <!-- Articles List -->
                    <?php 
                    while (have_posts()) : the_post();
                        $publication_date = pods_field('publication_date');
                        $volume = pods_field('volume');
                        $start_page = pods_field('start_page');
                        $end_page = pods_field('end_page');
                        $group_author = pods_field('group_author');
                        $main_title = pods_field('main_title');
                        $other_title = pods_field('other_title');

                        $date_value = $extract_value($publication_date);
                        $volume_value = $extract_value($volume);
                        $start_page_value = $extract_value($start_page);
                        $end_page_value = $extract_value($end_page);
                        ?>
                        <article class="art-item" data-aos="fade-up">
                            <div class="row g-3 flex-nowrap">
                                <div class="col overflow-hidden">
                                    <h3 class="art-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    

                                    <ul class="art-meta">
                                        <li>
                                            <?php 
                                            // Display group_author
                                            if (!empty($group_author) && is_array($group_author)): 
                                                echo esc_html(implode(', ', $group_author));
                                            endif;
                                            ?>
                                        </li>
                                        <?php if (!empty($date_value)): ?>
                                            <li><?php echo esc_html($date_value); ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($volume_value)): ?>
                                            <li><?php echo esc_html($volume_value); ?>巻</li>
                                        <?php endif; ?>
                                        <?php if (!empty($start_page_value) || !empty($end_page_value)): ?>
                                            <li>
                                                ページ：
                                               <?php if (!empty($start_page_value)): echo esc_html($start_page_value); endif; ?><?php if (!empty($start_page_value) && !empty($end_page_value)): echo '-'; endif; ?><?php if (!empty($end_page_value)): echo esc_html($end_page_value); endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    <?php 
                    endwhile;
                    ?>

                    <!-- Pagination -->
                    <?php brandwoods_pagination($wp_query); ?>
                </div>
            </div>
        </div>
    </section>
<?php else : ?>
    <section class="jr-articles" aria-labelledby="no-results-title">
        <div class="container-xxl">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9 text-center py-5">
                    <h2 id="no-results-title" class="h3 mb-3">No articles found</h2>
                    <?php if (!empty($search_query)) : ?>
                        <p>Sorry, no articles match your search for "<?php echo esc_html($search_query); ?>"</p>
                    <?php else : ?>
                        <p>Please enter a search term to find articles.</p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(home_url('/articles')); ?>" class="btn btn-viewmore mt-4">
                        <span>Browse All Articles</span>
                        <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11.5" stroke="white" />
                            <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_template_part('/template-parts/components/search', 'dual'); ?>

<style>
    .art-subtitle {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 0.5rem;
    }
</style>

<?php get_footer(); ?>
