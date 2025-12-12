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
            
            // Sanitize input to prevent XSS and other attacks
            $search_term = sanitize_text_field($search_term);
            
            // Validate and limit search term length
            if (strlen($search_term) > 200) {
                $search_term = substr($search_term, 0, 200);
            }
            
            if (!empty($search_term)) {
                // Remove default search behavior
                $query->set('s', '');
                
                // Escape search term for SQL LIKE query to prevent SQL injection
                global $wpdb;
                $escaped_term = $wpdb->esc_like($search_term);
                $like_term = '%' . $escaped_term . '%';
                
                // Use wpdb->prepare() for additional SQL injection protection
                $where_clause = $wpdb->prepare(
                    "t.post_title LIKE %s OR 
                    main_title.meta_value LIKE %s OR 
                    other_title.meta_value LIKE %s OR 
                    group_author.meta_value LIKE %s OR 
                    abstract.meta_value LIKE %s",
                    $like_term,
                    $like_term,
                    $like_term,
                    $like_term,
                    $like_term
                );
                
                // Search using Pods with better Japanese support
                // Limit results to prevent DoS attacks (max 1000 posts)
                $pods = pods('article', [
                    'limit' => 1000,
                    'where' => $where_clause,
                    'orderby' => 't.post_date DESC'
                ]);
                
                // Get post IDs from Pods result
                $post_ids = [];
                if ($pods->total() > 0) {
                    while ($pods->fetch()) {
                        $post_id = $pods->id();
                        // Validate post ID is a positive integer
                        if (is_numeric($post_id) && $post_id > 0) {
                            $post_ids[] = absint($post_id);
                        }
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
     * Returns escaped HTML to prevent XSS attacks
     */
    function brandwoods_get_search_term($escape = true) {
        global $wp_query;
        $search_term = $wp_query->get('search_term');
        if (empty($search_term)) {
            $search_term = get_search_query();
        }
        
        // Escape output to prevent XSS when displaying in HTML
        return $escape ? esc_html($search_term) : $search_term;
    }

    /**
     * Filter search, taxonomy, and archive results to exclude articles with invalid volume
     * - Exclude articles with volume = 0 or empty
     * - Exclude articles where volume has no other issues with the same volume
     */
    add_action('pre_get_posts', 'brandwoods_filter_by_volume');

    function brandwoods_filter_by_volume($query) {
        // Only apply to main query on frontend for search, taxonomy, and archive pages
        if (!is_admin() && $query->is_main_query() && ($query->is_search() || $query->is_tax() || $query->is_archive())) {
            
            // Add meta query to exclude empty or zero volumes
            $meta_query = array(
                'relation' => 'AND',
                array(
                    'key' => 'volume',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => 'volume',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => 'volume',
                    'value' => '0',
                    'compare' => '!='
                )
            );
            
            // Get existing meta query if any
            $existing_meta_query = $query->get('meta_query');
            if (!empty($existing_meta_query)) {
                $meta_query = array_merge(array($existing_meta_query), array($meta_query));
            }
            
            $query->set('meta_query', $meta_query);
            
            // Add filter to check if volume has related issues
            add_filter('posts_where', 'brandwoods_filter_volume_with_issues', 10, 2);
        }
    }

    /**
     * Check if volume has related issues (at least one other post with same volume)
     */
    function brandwoods_filter_volume_with_issues($where, $query) {
        global $wpdb;
        
        if (!is_admin() && $query->is_main_query() && ($query->is_search() || $query->is_tax() || $query->is_archive())) {
            // Only show posts where the volume exists in at least 2 posts (including itself)
            $where .= " AND {$wpdb->posts}.ID IN (
                SELECT pm1.post_id 
                FROM {$wpdb->postmeta} pm1
                WHERE pm1.meta_key = 'volume'
                AND pm1.meta_value != ''
                AND pm1.meta_value != '0'
                AND (
                    SELECT COUNT(DISTINCT pm2.post_id)
                    FROM {$wpdb->postmeta} pm2
                    WHERE pm2.meta_key = 'volume'
                    AND pm2.meta_value = pm1.meta_value
                ) > 1
            )";
            
            // Remove this filter after it's been applied once
            remove_filter('posts_where', 'brandwoods_filter_volume_with_issues', 10);
        }
        
        return $where;
    }

    /**
     * Hide default taxonomies (categories and tags) from admin menu
     * Keep them visible for default Posts
     */
    // Commented out - keep categories and tags visible for Posts
    // add_action('admin_menu', 'brandwoods_hide_default_taxonomies');
    // function brandwoods_hide_default_taxonomies() {
    //     remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
    //     remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    // }

    /**
     * Remove category and tag meta boxes from custom post types only
     * Keep them for default Posts
     */
    add_action('admin_init', 'brandwoods_remove_taxonomy_metaboxes');

    function brandwoods_remove_taxonomy_metaboxes() {
        // Only remove from custom post types, NOT from default 'post'
        $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
        foreach ($post_types as $post_type) {
            remove_meta_box('categorydiv', $post_type, 'side');
            remove_meta_box('tagsdiv-post_tag', $post_type, 'side');
        }
    }

    /**
     * Hide categories and tags columns from post list
     * Keep them visible for default Posts
     */
    // Commented out - keep columns visible for Posts
    // add_filter('manage_posts_columns', 'brandwoods_remove_taxonomy_columns');
    // add_filter('manage_pages_columns', 'brandwoods_remove_taxonomy_columns');
    // function brandwoods_remove_taxonomy_columns($columns) {
    //     unset($columns['categories']);
    //     unset($columns['tags']);
    //     return $columns;
    // }

    /**
     * Remove quick edit option for categories and tags from custom post types only
     * Using both admin_head and admin_footer to ensure it works
     */
    add_action('admin_head', 'brandwoods_hide_quick_edit_taxonomies');
    add_action('admin_footer', 'brandwoods_hide_quick_edit_taxonomies');

    function brandwoods_hide_quick_edit_taxonomies() {
        global $typenow, $pagenow;
        
        // Get current screen
        $screen = get_current_screen();
        
        // Check if we're on a custom post type edit page
        $is_custom_post_type = false;
        
        if ($screen && isset($screen->post_type)) {
            $is_custom_post_type = !in_array($screen->post_type, ['post', 'page', 'attachment']);
        } elseif ($typenow) {
            $is_custom_post_type = !in_array($typenow, ['post', 'page', 'attachment']);
        }
        
        // Hide for custom post types only
        if ($is_custom_post_type || (in_array($pagenow, ['edit.php']) && isset($_GET['post_type']))) {
            echo '<style>
                /* Hide quick edit categories and tags */
                .inline-edit-categories,
                .inline-edit-tags,
                fieldset.inline-edit-categories,
                fieldset.inline-edit-tags {
                    display: none !important;
                }
                
                /* Hide taxonomy filters in admin list */
                #category-filter-link,
                #tag-filter-link {
                    display: none !important;
                }
            </style>';
            
            // Also hide via JavaScript for dynamic content
            echo '<script>
                jQuery(document).ready(function($) {
                    // Hide categories and tags in quick edit
                    $(".inline-edit-categories, .inline-edit-tags").hide();
                    
                    // Remove from bulk edit
                    $("#bulk-edit .inline-edit-categories, #bulk-edit .inline-edit-tags").hide();
                });
            </script>';
        }
    }

    /**
     * Unregister categories and tags from custom post types
     */
    add_action('init', 'brandwoods_unregister_taxonomies_from_custom_post_types', 999);

    function brandwoods_unregister_taxonomies_from_custom_post_types() {
        $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
        
        foreach ($post_types as $post_type) {
            // Unregister category
            unregister_taxonomy_for_object_type('category', $post_type);
            // Unregister post_tag
            unregister_taxonomy_for_object_type('post_tag', $post_type);
        }
    }

    /**
     * Remove taxonomy boxes from custom post types in admin menu
     */
    add_action('admin_menu', 'brandwoods_remove_taxonomy_submenus', 999);

    function brandwoods_remove_taxonomy_submenus() {
        // Get all custom post types
        $post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
        
        foreach ($post_types as $post_type) {
            // Remove categories submenu
            remove_submenu_page('edit.php?post_type=' . $post_type, 'edit-tags.php?taxonomy=category&post_type=' . $post_type);
            
            // Remove tags submenu
            remove_submenu_page('edit.php?post_type=' . $post_type, 'edit-tags.php?taxonomy=post_tag&post_type=' . $post_type);
        }
    }

    /**
     * Filter for taxonomy issue queries - check if issue volume has related articles
     */
    function brandwoods_taxonomy_filter_issue_volume_with_articles($where, $query) {
        global $wpdb;
        
        // Only show issues where the volume exists in at least one article
        $where .= " AND {$wpdb->posts}.ID IN (
            SELECT DISTINCT p.ID
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm_issue ON p.ID = pm_issue.post_id
            WHERE p.post_type = 'issue'
            AND pm_issue.meta_key = 'volume'
            AND pm_issue.meta_value != ''
            AND pm_issue.meta_value != '0'
            AND EXISTS (
                SELECT 1
                FROM {$wpdb->posts} p2
                INNER JOIN {$wpdb->postmeta} pm_article ON p2.ID = pm_article.post_id
                WHERE p2.post_type = 'article'
                AND pm_article.meta_key = 'volume'
                AND pm_article.meta_value = pm_issue.meta_value
                AND pm_article.meta_value != ''
                AND pm_article.meta_value != '0'
            )
        )";
        
        return $where;
    }

    

?>