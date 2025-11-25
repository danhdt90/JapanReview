<?php 
    define('ASSETS_PATH', get_stylesheet_directory_uri() . '/assets');
    define('STYLESHEET_PATH', ASSETS_PATH . '/css');
    define('STYLESHEET_PATH_FE', ASSETS_PATH . '/scss');
    define('SCRIPT_PATH', ASSETS_PATH . '/js');
    define('IMAGE_PATH', ASSETS_PATH . '/images');

    include_once('includes/post-type.php');

    function brandwoods_setup(){

        load_theme_textdomain( 'brandwoods2025' );

        add_theme_support( 'title-tag' );

        add_theme_support( 'post-thumbnails' );

        register_nav_menus( array (
            'menu_header' => __('Menu Header', 'brandwoods2025'),
            'menu_footer' => __('Menu Footer', 'brandwoods2025'),
        ) );
    }

    add_action( 'after_setup_theme', 'brandwoods_setup');

    function brandwoods_enqueue_scripts() {
        wp_enqueue_style(
            'brandwoods-be-style',
            get_template_directory_uri() . '/assets/css/styles-be.css',
            [],
            '1.0'
        );

        wp_enqueue_style(
            'brandwoods-be-style-default',
            get_template_directory_uri() . '/style.css',
            [],
            '1.0'
        );
    
        
        wp_enqueue_script(
            'brandwoods-be-script',
            get_template_directory_uri() .'/assets/js/main-be.js',
            ['jquery'],
            '1.0'
        );
    }
    
    add_action('wp_enqueue_scripts', 'brandwoods_enqueue_scripts');


    // Registers custom block styles.
    if ( ! function_exists( 'brandwoods_block_styles' ) ) :
        /**
         * Registers custom block styles.
         *
         * @since Twenty Twenty-Five 1.0
         *
         * @return void
         */
        function brandwoods_block_styles() {
            register_block_style(
                'core/list',
                array(
                    'name'         => 'checkmark-list',
                    'label'        => __( 'Checkmark', 'brandwoods2025' ),
                    'inline_style' => '
                    ul.is-style-checkmark-list {
                        list-style-type: "\2713";
                    }

                    ul.is-style-checkmark-list li {
                        padding-inline-start: 1ch;
                    }',
                )
            );
        }
    endif;
    add_action( 'init', 'brandwoods_block_styles' );

    // Registers pattern categories.
    if ( ! function_exists( 'brandwoods_pattern_categories' ) ) :
        /**
         * Registers pattern categories.
         *
         * @since Twenty Twenty-Five 1.0
         *
         * @return void
         */
        function brandwoods_pattern_categories() {

            register_block_pattern_category(
                'brandwoods_page',
                array(
                    'label'       => __( 'Pages', 'brandwoods2025' ),
                    'description' => __( 'A collection of full page layouts.', 'brandwoods2025' ),
                )
            );

            register_block_pattern_category(
                'brandwoods_post-format',
                array(
                    'label'       => __( 'Post formats', 'brandwoods2025' ),
                    'description' => __( 'A collection of post format patterns.', 'brandwoods2025' ),
                )
            );
        }
    endif;
    add_action( 'init', 'brandwoods_pattern_categories' );

    if ( ! function_exists( 'brandwoods_render_menu' ) ) :

        function brandwoods_render_menu($name) {
    
            $menuLocations = get_nav_menu_locations();

            if (!empty($menuLocations )) {
                $navbar_items = wp_get_nav_menu_items($menuLocations[$name]);
                $child_items = [];

                if($navbar_items) {
                    foreach ($navbar_items as $key => $item) {
                        if ($item->menu_item_parent) {
                            array_push($child_items, $item);
                            unset($navbar_items[$key]);
                        }
                    }
                }
                
                if($navbar_items) {
                    foreach ($navbar_items as $item) {
                        foreach ($child_items as $key => $child) {
                            if ($child->menu_item_parent == $item->ID) {
                                if (!$item->child_items) {
                                    $item->child_items = [];
                                }
            
                                array_push($item->child_items, $child);
            
                                unset($child_items[$key]);
                            }
                        }
                    }
                }
                return $navbar_items;
            }
        }
    
    endif;

    if ( ! function_exists( 'brandwoods_pagination' )) :

        function brandwoods_pagination($query = null) {
            if ($query === null) {
                global $wp_query;
                $query = $wp_query;
            }
        
            $big = 999999999; 
        
            if ($query->max_num_pages <= 1) {
                return;
            }
        
            $current = max(1, get_query_var('paged'));
            $total   = $query->max_num_pages;
        
            // Default page
            $pages_to_show = [1, 2, $total];
        
            for ($i = $current - 1; $i <= $current + 1; $i++) {
                if ($i > 0 && $i <= $total) {
                    $pages_to_show[] = $i;
                }
            }
        
            $pages_to_show = array_unique($pages_to_show);
            sort($pages_to_show);
        
            echo '<nav class="jr-pagination mt-4" aria-label="News pagination">';
            echo '<ul class="pagination justify-content-center gap-2">';
        
            if ($current > 1) {
                echo '<li class="page-item"><a class="page-link" href="' . esc_url(get_pagenum_link($current - 1)) . '">‹</a></li>';
            } else {
                echo '<li class="page-item disabled"><span class="page-link">‹</span></li>';
            }
        
            $last_page = 0;
            foreach ($pages_to_show as $page_num) {
                if ($page_num - $last_page > 1) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }
        
                if ($page_num == $current) {
                    echo '<li class="page-item active"><span class="page-link">' . $page_num . '</span></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="' . esc_url(get_pagenum_link($page_num)) . '">' . $page_num . '</a></li>';
                }
        
                $last_page = $page_num;
            }
        
            if ($current < $total) {
                echo '<li class="page-item"><a class="page-link" href="' . esc_url(get_pagenum_link($current + 1)) . '">›</a></li>';
            } else {
                echo '<li class="page-item disabled"><span class="page-link">›</span></li>';
            }
        
            echo '</ul></nav>';
        }
        
    endif;

    function add_issues_pagination_rewrite() {
        add_rewrite_rule(
            '^issues/page/([0-9]+)/?',
            'index.php?pagename=issues&paged=$matches[1]',
            'top'
        );
    }
    add_action('init', 'add_issues_pagination_rewrite');

    // Rewrite post/post-name => news/post-name
    function brandwoods_add_news_rewrite_rules() {
        add_rewrite_rule(
            '^news/([^/]+)/?$',
            'index.php?name=$matches[1]',
            'top'
        );
    }
    add_action('init', 'brandwoods_add_news_rewrite_rules');
    function brandwoods_add_news_permalink($permalink, $post, $leavename) {
        if ($post->post_type === 'post') {
            return home_url('/news/' . $post->post_name . '/');
        }
        return $permalink;
    }
    add_filter('post_link', 'brandwoods_add_news_permalink', 10, 3);

    
    // check unique volume issuse
    add_filter('acf/validate_value/name=volume', function( $valid, $value, $field, $input ) {
        if ( !$valid || empty($value) ) {
            return $valid;
        }
    
        $current_post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
    
        $args = [
            'post_type'      => 'issue',
            'post_status'    => 'any',
            'meta_key'       => 'volume',
            'meta_value'     => $value,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'post__not_in'   => [$current_post_id],
        ];
    
        $existing = get_posts($args);
    
        if ( $existing ) {
            $valid = 'This volume already exists. Please enter another number.';
        }
    
        return $valid;
    }, 10, 4);

    /**
     * Improve search for Japanese characters using Pods API
     */
    function brandwoods_custom_search_query($query) {
        // Only modify main search query on frontend
        if (!is_admin() && $query->is_main_query() && $query->is_search()) {
            $search_term = $query->get('s');
            
            if (!empty($search_term)) {
                // Remove default search behavior
                $query->set('s', '');
                
                // Search using Pods with better Japanese support
                $pods = pods('article', [
                    'limit' => -1,
                    'where' => sprintf(
                        "t.post_title LIKE '%%%s%%' OR 
                        main_title.meta_value LIKE '%%%s%%' OR 
                        other_title.meta_value LIKE '%%%s%%' OR 
                        group_author.meta_value LIKE '%%%s%%' OR 
                        abstract.meta_value LIKE '%%%s%%'",
                        $GLOBALS['wpdb']->esc_like($search_term),
                        $GLOBALS['wpdb']->esc_like($search_term),
                        $GLOBALS['wpdb']->esc_like($search_term),
                        $GLOBALS['wpdb']->esc_like($search_term),
                        $GLOBALS['wpdb']->esc_like($search_term)
                    ),
                    'orderby' => 't.post_date DESC'
                ]);
                
                // Get post IDs from Pods result
                $post_ids = [];
                if ($pods->total() > 0) {
                    while ($pods->fetch()) {
                        $post_ids[] = $pods->id();
                    }
                }
                
                // If we found posts, set them as the search result
                if (!empty($post_ids)) {
                    $query->set('post__in', $post_ids);
                    $query->set('orderby', 'post__in');
                } else {
                    // No results found, set impossible condition
                    $query->set('post__in', [0]);
                }
                
                // Store search term for highlighting
                $query->set('search_term', $search_term);
            }
        }
        
        return $query;
    }
    add_action('pre_get_posts', 'brandwoods_custom_search_query');

    /**
     * Get search term from query for highlighting
     */
    function brandwoods_get_search_term() {
        global $wp_query;
        $search_term = $wp_query->get('search_term');
        return !empty($search_term) ? $search_term : get_search_query();
    }

?>