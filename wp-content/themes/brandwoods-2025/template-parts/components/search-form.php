<?php
/**
 * Search Form Component
 * 
 * Displays a search form with modern styling
 * 
 * @param array $args {
 *     Component arguments
 *     @type string $form_class     Additional CSS class for the form
 *     @type string $placeholder    Placeholder text
 *     @type string $button_text    Search button text
 *     @type bool   $show_filters   Whether to show search filters
 *     @type string $search_type    Type of search (books, posts, all)
 * }
 */

// Set default arguments
$defaults = array(
    'form_class'   => '',
    'placeholder'  => __('Search...', 'brandwoods-2025'),
    'button_text'  => __('Search', 'brandwoods-2025'),
    'show_filters' => false,
    'search_type'  => 'all',
);

$args = wp_parse_args($args ?? array(), $defaults);

// Get current search query
$current_search = get_search_query();
?>

<div class="search-component <?php echo esc_attr($args['form_class']); ?>">
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="search-form__wrapper">
            
            <div class="search-form__input-group">
                <input 
                    type="search" 
                    class="search-form__input" 
                    placeholder="<?php echo esc_attr($args['placeholder']); ?>"
                    value="<?php echo esc_attr($current_search); ?>" 
                    name="s" 
                    title="<?php echo esc_attr($args['placeholder']); ?>"
                    autocomplete="off"
                />
                
                <button type="submit" class="search-form__button">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span class="search-button-text"><?php echo esc_html($args['button_text']); ?></span>
                </button>
            </div>

            <?php if ($args['show_filters']): ?>
            <div class="search-form__filters">
                <div class="search-filters">
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Search in:', 'brandwoods-2025'); ?></label>
                        <select name="post_type" class="filter-select">
                            <option value=""><?php _e('All Content', 'brandwoods-2025'); ?></option>
                            <option value="book" <?php selected(get_query_var('post_type'), 'book'); ?>>
                                <?php _e('Books', 'brandwoods-2025'); ?>
                            </option>
                            <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>>
                                <?php _e('Posts', 'brandwoods-2025'); ?>
                            </option>
                            <option value="page" <?php selected(get_query_var('post_type'), 'page'); ?>>
                                <?php _e('Pages', 'brandwoods-2025'); ?>
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Category:', 'brandwoods-2025'); ?></label>
                        <?php
                        wp_dropdown_categories(array(
                            'show_option_all' => __('All Categories', 'brandwoods-2025'),
                            'name'            => 'cat',
                            'class'           => 'filter-select',
                            'selected'        => get_query_var('cat'),
                            'hierarchical'    => true,
                        ));
                        ?>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Date:', 'brandwoods-2025'); ?></label>
                        <select name="year" class="filter-select">
                            <option value=""><?php _e('Any Time', 'brandwoods-2025'); ?></option>
                            <?php
                            $current_year = date('Y');
                            for ($year = $current_year; $year >= $current_year - 10; $year--):
                            ?>
                            <option value="<?php echo $year; ?>" <?php selected(get_query_var('year'), $year); ?>>
                                <?php echo $year; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>
            </div>
            <?php endif; ?>

        </div>

        <?php if ($args['search_type'] !== 'all'): ?>
        <input type="hidden" name="post_type" value="<?php echo esc_attr($args['search_type']); ?>" />
        <?php endif; ?>

    </form>

    <?php if ($current_search): ?>
    <div class="search-form__results-info">
        <p class="search-results-text">
            <?php
            printf(
                __('Search results for: <strong>%s</strong>', 'brandwoods-2025'),
                esc_html($current_search)
            );
            ?>
        </p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="clear-search">
            <?php _e('Clear search', 'brandwoods-2025'); ?>
        </a>
    </div>
    <?php endif; ?>

</div>