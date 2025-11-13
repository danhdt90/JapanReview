<?php
    // Registers block binding sources.
    if ( ! function_exists( 'brandwoods_register_block_bindings' ) ) :
        /**
         * Registers the post format block binding source.
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_block_bindings() {
            register_block_bindings_source(
                'brandwoods2025/format',
                array(
                    'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'brandwoods2025' ),
                    'get_value_callback' => 'brandwoods_format_binding',
                )
            );
        }
    endif;
    add_action( 'init', 'brandwoods_register_block_bindings' );

    // Registers block binding callback function for the post format name.
    if ( ! function_exists( 'brandwoods_format_binding' ) ) :
        /**
         * Callback function for the post format name block binding source.
         *
         * @since Brandwoods 2025
         *
         * @return string|void Post format name, or nothing if the format is 'standard'.
         */
        function brandwoods_format_binding() {
            $post_format_slug = get_post_format();

            if ( $post_format_slug && 'standard' !== $post_format_slug ) {
                return get_post_format_string( $post_format_slug );
            }
        }
    endif;

    // Register Custom Post Type: Article
    if ( ! function_exists( 'brandwoods_register_article_post_type' ) ) :
        /**
         * Register Article Custom Post Type
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_article_post_type() {
            $labels = array(
                'name'                  => _x( 'Articles', 'Post Type General Name', 'brandwoods2025' ),
                'singular_name'         => _x( 'Article', 'Post Type Singular Name', 'brandwoods2025' ),
                'menu_name'             => __( 'Articles', 'brandwoods2025' ),
                'name_admin_bar'        => __( 'Article', 'brandwoods2025' ),
                'archives'              => __( 'Article Archives', 'brandwoods2025' ),
                'attributes'            => __( 'Article Attributes', 'brandwoods2025' ),
                'parent_item_colon'     => __( 'Parent Article:', 'brandwoods2025' ),
                'all_items'             => __( 'All Articles', 'brandwoods2025' ),
                'add_new_item'          => __( 'Add New Article', 'brandwoods2025' ),
                'add_new'               => __( 'Add New', 'brandwoods2025' ),
                'new_item'              => __( 'New Article', 'brandwoods2025' ),
                'edit_item'             => __( 'Edit Article', 'brandwoods2025' ),
                'update_item'           => __( 'Update Article', 'brandwoods2025' ),
                'view_item'             => __( 'View Article', 'brandwoods2025' ),
                'view_items'            => __( 'View Articles', 'brandwoods2025' ),
                'search_items'          => __( 'Search Article', 'brandwoods2025' ),
                'not_found'             => __( 'Not found', 'brandwoods2025' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'brandwoods2025' ),
                'featured_image'        => __( 'Featured Image', 'brandwoods2025' ),
                'set_featured_image'    => __( 'Set featured image', 'brandwoods2025' ),
                'remove_featured_image' => __( 'Remove featured image', 'brandwoods2025' ),
                'use_featured_image'    => __( 'Use as featured image', 'brandwoods2025' ),
                'insert_into_item'      => __( 'Insert into article', 'brandwoods2025' ),
                'uploaded_to_this_item' => __( 'Uploaded to this article', 'brandwoods2025' ),
                'items_list'            => __( 'Articles list', 'brandwoods2025' ),
                'items_list_navigation' => __( 'Articles list navigation', 'brandwoods2025' ),
                'filter_items_list'     => __( 'Filter articles list', 'brandwoods2025' ),
            );

            $args = array(
                'label'                 => __( 'Article', 'brandwoods2025' ),
                'description'           => __( 'A custom post type for articles', 'brandwoods2025' ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions', 'author' ),
                'taxonomies'            => array( 'category', 'post_tag' ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'menu_icon'             => 'dashicons-book',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'post',
                'show_in_rest'          => true,
                'rest_base'             => 'articles',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
                'rewrite'               => array(
                    'slug'       => 'articles',
                    'with_front' => false,
                ),
            );

            register_post_type( 'article', $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_article_post_type', 0 );

    // Register Custom Taxonomy for Articles: Genre
    if ( ! function_exists( 'brandwoods_register_article_genre_taxonomy' ) ) :
        /**
         * Register Article Genre Taxonomy
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_article_genre_taxonomy() {
            $labels = array(
                'name'                       => _x( 'Genres', 'Taxonomy General Name', 'brandwoods2025' ),
                'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'brandwoods2025' ),
                'menu_name'                  => __( 'Genres', 'brandwoods2025' ),
                'all_items'                  => __( 'All Genres', 'brandwoods2025' ),
                'parent_item'                => __( 'Parent Genre', 'brandwoods2025' ),
                'parent_item_colon'          => __( 'Parent Genre:', 'brandwoods2025' ),
                'new_item_name'              => __( 'New Genre Name', 'brandwoods2025' ),
                'add_new_item'               => __( 'Add New Genre', 'brandwoods2025' ),
                'edit_item'                  => __( 'Edit Genre', 'brandwoods2025' ),
                'update_item'                => __( 'Update Genre', 'brandwoods2025' ),
                'view_item'                  => __( 'View Genre', 'brandwoods2025' ),
                'separate_items_with_commas' => __( 'Separate genres with commas', 'brandwoods2025' ),
                'add_or_remove_items'        => __( 'Add or remove genres', 'brandwoods2025' ),
                'choose_from_most_used'      => __( 'Choose from the most used', 'brandwoods2025' ),
                'popular_items'              => __( 'Popular Genres', 'brandwoods2025' ),
                'search_items'               => __( 'Search Genres', 'brandwoods2025' ),
                'not_found'                  => __( 'Not Found', 'brandwoods2025' ),
                'no_terms'                   => __( 'No genres', 'brandwoods2025' ),
                'items_list'                 => __( 'Genres list', 'brandwoods2025' ),
                'items_list_navigation'      => __( 'Genres list navigation', 'brandwoods2025' ),
            );

            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => true,
                'public'                     => true,
                'show_ui'                    => true,
                'show_admin_column'          => true,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => true,
                'show_in_rest'               => true,
                'rest_base'                  => 'article-genres',
                'rest_controller_class'      => 'WP_REST_Terms_Controller',
                'rewrite'                    => array(
                    'slug'       => 'article-genre',
                    'with_front' => false,
                ),
            );

            register_taxonomy( 'article_genre', array( 'article' ), $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_article_genre_taxonomy', 0 );

    // Flush rewrite rules on theme activation
    function brandwoods_flush_rewrite_rules() {
        brandwoods_register_article_post_type();
        brandwoods_register_article_genre_taxonomy();
        flush_rewrite_rules();
    }
    add_action( 'after_switch_theme', 'brandwoods_flush_rewrite_rules' );

    if ( ! function_exists( 'brandwoods_register_issues_post_type' ) ) :
        /**
         * Register Issues Custom Post Type
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_issues_post_type() {
            $labels = array(
                'name'                  => _x( 'Issues', 'Post Type General Name', 'brandwoods2025' ),
                'singular_name'         => _x( 'Issues', 'Post Type Singular Name', 'brandwoods2025' ),
                'menu_name'             => __( 'Issues', 'brandwoods2025' ),
                'name_admin_bar'        => __( 'Issues', 'brandwoods2025' ),
                'archives'              => __( 'Issues Archives', 'brandwoods2025' ),
                'attributes'            => __( 'Issues Attributes', 'brandwoods2025' ),
                'parent_item_colon'     => __( 'Parent Issues:', 'brandwoods2025' ),
                'all_items'             => __( 'All Issues', 'brandwoods2025' ),
                'add_new_item'          => __( 'Add New Issues', 'brandwoods2025' ),
                'add_new'               => __( 'Add New', 'brandwoods2025' ),
                'new_item'              => __( 'New Issues', 'brandwoods2025' ),
                'edit_item'             => __( 'Edit Issues', 'brandwoods2025' ),
                'update_item'           => __( 'Update Issues', 'brandwoods2025' ),
                'view_item'             => __( 'View Issues', 'brandwoods2025' ),
                'view_items'            => __( 'View Issues', 'brandwoods2025' ),
                'search_items'          => __( 'Search Issues', 'brandwoods2025' ),
                'not_found'             => __( 'Not found', 'brandwoods2025' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'brandwoods2025' ),
                'featured_image'        => __( 'Featured Image', 'brandwoods2025' ),
                'set_featured_image'    => __( 'Set featured image', 'brandwoods2025' ),
                'remove_featured_image' => __( 'Remove featured image', 'brandwoods2025' ),
                'use_featured_image'    => __( 'Use as featured image', 'brandwoods2025' ),
                'insert_into_item'      => __( 'Insert into issues', 'brandwoods2025' ),
                'uploaded_to_this_item' => __( 'Uploaded to this issues', 'brandwoods2025' ),
                'items_list'            => __( 'Issues list', 'brandwoods2025' ),
                'items_list_navigation' => __( 'Issues list navigation', 'brandwoods2025' ),
                'filter_items_list'     => __( 'Filter issues list', 'brandwoods2025' ),
            );

            $args = array(
                'label'                 => __( 'Issues', 'brandwoods2025' ),
                'description'           => __( 'A custom post type for issues', 'brandwoods2025' ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions', 'author' ),
                // 'taxonomies'            => array( 'category', 'post_tag' ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'menu_icon'             => 'dashicons-list-view',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'post',
                'show_in_rest'          => true,
                'rest_base'             => 'issue',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
                'rewrite'               => array(
                    'slug'       => 'issues',
                    'with_front' => false,
                ),
            );

            register_post_type( 'issue', $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_issues_post_type', 0 );
    
?>