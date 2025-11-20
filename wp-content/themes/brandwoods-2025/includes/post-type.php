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
                'show_tagcloud'              => false,
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

    // Custom meta box for Genre taxonomy (radio buttons - single selection)
    if ( ! function_exists( 'brandwoods_article_genre_radio_meta_box' ) ) :
        /**
         * Custom meta box for Genre taxonomy with radio buttons
         *
         * @since Brandwoods 2025
         *
         * @param WP_Post $post The post object.
         * @return void
         */
        function brandwoods_article_genre_radio_meta_box( $post ) {
            $taxonomy = 'article_genre';
            $terms = get_terms( array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            ) );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                echo '<p>' . __( 'No genres available.', 'brandwoods2025' ) . '</p>';
                return;
            }

            $current_term = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
            $current_term_id = ! empty( $current_term ) ? $current_term[0] : 0;

            echo '<div id="taxonomy-' . esc_attr( $taxonomy ) . '" class="categorydiv">';
            echo '<input type="hidden" name="tax_input[' . esc_attr( $taxonomy ) . '][]" value="0" />';
            
            foreach ( $terms as $term ) {
                $id = 'genre-' . $term->term_id;
                echo '<label for="' . esc_attr( $id ) . '" class="selectit">';
                echo '<input type="radio" id="' . esc_attr( $id ) . '" name="tax_input[' . esc_attr( $taxonomy ) . '][]" value="' . esc_attr( $term->term_id ) . '" ' . checked( $current_term_id, $term->term_id, false ) . ' /> ';
                echo esc_html( $term->name );
                echo '</label><br>';
            }
            
            echo '</div>';
        }
    endif;

    // Replace the default meta box with custom radio button meta box
    if ( ! function_exists( 'brandwoods_replace_genre_meta_box' ) ) :
        /**
         * Replace the default genre meta box with custom radio button version
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_replace_genre_meta_box() {
            remove_meta_box( 'article_genrediv', 'article', 'side' );
            add_meta_box(
                'article_genre_radio',
                __( 'Genre', 'brandwoods2025' ),
                'brandwoods_article_genre_radio_meta_box',
                'article',
                'side',
                'default'
            );
        }
    endif;
    add_action( 'add_meta_boxes', 'brandwoods_replace_genre_meta_box' );

    // Register Custom Taxonomy for Articles: Keywords
    if ( ! function_exists( 'brandwoods_register_article_keywords_taxonomy' ) ) :
        /**
         * Register Article Keywords Taxonomy
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_article_keywords_taxonomy() {
            $labels = array(
                'name'                       => _x( 'Keywords', 'Taxonomy General Name', 'brandwoods2025' ),
                'singular_name'              => _x( 'Keyword', 'Taxonomy Singular Name', 'brandwoods2025' ),
                'menu_name'                  => __( 'Keywords', 'brandwoods2025' ),
                'all_items'                  => __( 'All Keywords', 'brandwoods2025' ),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'new_item_name'              => __( 'New Keyword Name', 'brandwoods2025' ),
                'add_new_item'               => __( 'Add New Keyword', 'brandwoods2025' ),
                'edit_item'                  => __( 'Edit Keyword', 'brandwoods2025' ),
                'update_item'                => __( 'Update Keyword', 'brandwoods2025' ),
                'view_item'                  => __( 'View Keyword', 'brandwoods2025' ),
                'separate_items_with_commas' => __( 'Separate keywords with commas', 'brandwoods2025' ),
                'add_or_remove_items'        => __( 'Add or remove keywords', 'brandwoods2025' ),
                'choose_from_most_used'      => __( 'Choose from the most used keywords', 'brandwoods2025' ),
                'popular_items'              => __( 'Popular Keywords', 'brandwoods2025' ),
                'search_items'               => __( 'Search Keywords', 'brandwoods2025' ),
                'not_found'                  => __( 'No keywords found', 'brandwoods2025' ),
                'no_terms'                   => __( 'No keywords', 'brandwoods2025' ),
                'items_list'                 => __( 'Keywords list', 'brandwoods2025' ),
                'items_list_navigation'      => __( 'Keywords list navigation', 'brandwoods2025' ),
            );

            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => false, // Like tags, not categories
                'public'                     => true,
                'show_ui'                    => true,
                'show_admin_column'          => true,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => true,
                'show_in_rest'               => true,
                'rest_base'                  => 'keywords-article',
                'rest_controller_class'      => 'WP_REST_Terms_Controller',
                'rewrite'                    => array(
                    'slug'       => 'keyword',
                    'with_front' => false,
                ),
            );

            register_taxonomy( 'keywords_article', array( 'article' ), $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_article_keywords_taxonomy', 0 );

    // Register Custom Taxonomy for Articles: Publication Year
    if ( ! function_exists( 'brandwoods_register_article_year_taxonomy' ) ) :
        /**
         * Register Article Publication Year Taxonomy
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_article_year_taxonomy() {
            $labels = array(
                'name'              => _x( 'Publication Years', 'Taxonomy General Name', 'brandwoods2025' ),
                'singular_name'     => _x( 'Publication Year', 'Taxonomy Singular Name', 'brandwoods2025' ),
                'search_items'      => __( 'Search Years', 'brandwoods2025' ),
                'all_items'         => __( 'All Years', 'brandwoods2025' ),
                'edit_item'         => __( 'Edit Year', 'brandwoods2025' ),
                'update_item'       => __( 'Update Year', 'brandwoods2025' ),
                'add_new_item'      => __( 'Add New Year', 'brandwoods2025' ),
                'new_item_name'     => __( 'New Year Name', 'brandwoods2025' ),
                'menu_name'         => __( 'Publication Years', 'brandwoods2025' ),
            );

            $args = array(
                'labels'            => $labels,
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
                'show_in_rest'      => true,
                'rest_base'         => 'article-years',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
                'rewrite'           => array(
                    'slug'       => 'year',
                    'with_front' => false,
                ),
            );

            register_taxonomy( 'article_year', array( 'article' ), $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_article_year_taxonomy', 0 );

    // Register Custom Taxonomy for Articles: Early Access
    if ( ! function_exists( 'brandwoods_register_article_early_access_taxonomy' ) ) :
        /**
         * Register Article Early Access Taxonomy
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_article_early_access_taxonomy() {
            $labels = array(
                'name'              => _x( 'Early Access', 'Taxonomy General Name', 'brandwoods2025' ),
                'singular_name'     => _x( 'Early Access', 'Taxonomy Singular Name', 'brandwoods2025' ),
                'menu_name'         => __( 'Early Access', 'brandwoods2025' ),
                'all_items'         => __( 'All Early Access', 'brandwoods2025' ),
                'edit_item'         => __( 'Edit Early Access', 'brandwoods2025' ),
                'update_item'       => __( 'Update Early Access', 'brandwoods2025' ),
                'add_new_item'      => __( 'Add New Early Access', 'brandwoods2025' ),
                'new_item_name'     => __( 'New Early Access Name', 'brandwoods2025' ),
                'search_items'      => __( 'Search Early Access', 'brandwoods2025' ),
                'not_found'         => __( 'No early access found', 'brandwoods2025' ),
            );

            $args = array(
                'labels'            => $labels,
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
                'show_in_rest'      => true,
                'rest_base'         => 'article-early-access',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
                'rewrite'           => array(
                    'slug'       => 'early-access',
                    'with_front' => false,
                ),
            );

            register_taxonomy( 'article_early_access', array( 'article' ), $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_article_early_access_taxonomy', 0 );

    // Custom meta box for Year taxonomy (radio buttons - single selection)
    if ( ! function_exists( 'brandwoods_article_year_radio_meta_box' ) ) :
        /**
         * Custom meta box for Year taxonomy with radio buttons
         *
         * @since Brandwoods 2025
         *
         * @param WP_Post $post The post object.
         * @return void
         */
        function brandwoods_article_year_radio_meta_box( $post ) {
            $taxonomy = 'article_year';
            $terms = get_terms( array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'DESC',
            ) );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                echo '<p>' . __( 'No years available.', 'brandwoods2025' ) . '</p>';
                return;
            }

            $current_term = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
            $current_term_id = ! empty( $current_term ) ? $current_term[0] : 0;

            echo '<div id="taxonomy-' . esc_attr( $taxonomy ) . '" class="categorydiv">';
            echo '<input type="hidden" name="tax_input[' . esc_attr( $taxonomy ) . '][]" value="0" />';
            
            foreach ( $terms as $term ) {
                $id = 'year-' . $term->term_id;
                echo '<label for="' . esc_attr( $id ) . '" class="selectit">';
                echo '<input type="radio" id="' . esc_attr( $id ) . '" name="tax_input[' . esc_attr( $taxonomy ) . '][]" value="' . esc_attr( $term->term_id ) . '" ' . checked( $current_term_id, $term->term_id, false ) . ' /> ';
                echo esc_html( $term->name );
                echo '</label><br>';
            }
            
            echo '</div>';
        }
    endif;

    // Replace the default meta box with custom radio button meta box
    if ( ! function_exists( 'brandwoods_replace_year_meta_box' ) ) :
        /**
         * Replace the default year meta box with custom radio button version
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_replace_year_meta_box() {
            remove_meta_box( 'article_yeardiv', 'article', 'side' );
            add_meta_box(
                'article_year_radio',
                __( 'Publication Year', 'brandwoods2025' ),
                'brandwoods_article_year_radio_meta_box',
                'article',
                'side',
                'default'
            );
        }
    endif;
    add_action( 'add_meta_boxes', 'brandwoods_replace_year_meta_box' );

    // Custom meta box for Early Access taxonomy (checkbox - flag)
    if ( ! function_exists( 'brandwoods_article_early_access_checkbox_meta_box' ) ) :
        /**
         * Custom meta box for Early Access taxonomy with checkbox
         *
         * @since Brandwoods 2025
         *
         * @param WP_Post $post The post object.
         * @return void
         */
        function brandwoods_article_early_access_checkbox_meta_box( $post ) {
            $taxonomy = 'article_early_access';
            
            // Get or create the "Early Access" term
            $term = get_term_by('slug', 'early-access', $taxonomy);
            if (!$term) {
                $result = wp_insert_term('Early Access', $taxonomy, array('slug' => 'early-access'));
                if (!is_wp_error($result)) {
                    $term = get_term($result['term_id'], $taxonomy);
                }
            }
            
            if (!$term || is_wp_error($term)) {
                echo '<p>' . __( 'Error loading Early Access option.', 'brandwoods2025' ) . '</p>';
                return;
            }

            $current_terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
            $is_checked = in_array($term->term_id, $current_terms);

            wp_nonce_field('early_access_nonce', 'early_access_nonce');
            
            echo '<label>';
            echo '<input type="checkbox" name="is_early_access" value="1" ' . checked($is_checked, true, false) . ' /> ';
            echo __('Mark as Early Access Article', 'brandwoods2025');
            echo '</label>';
        }
    endif;

    // Replace the default meta box with custom checkbox meta box for Early Access
    if ( ! function_exists( 'brandwoods_replace_early_access_meta_box' ) ) :
        /**
         * Replace the default early access meta box with custom checkbox version
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_replace_early_access_meta_box() {
            remove_meta_box( 'article_early_accessdiv', 'article', 'side' );
            add_meta_box(
                'article_early_access_checkbox',
                __( 'Early Access', 'brandwoods2025' ),
                'brandwoods_article_early_access_checkbox_meta_box',
                'article',
                'side',
                'high'
            );
        }
    endif;
    add_action( 'add_meta_boxes', 'brandwoods_replace_early_access_meta_box' );

    // Save Early Access checkbox
    if ( ! function_exists( 'brandwoods_save_early_access_meta' ) ) :
        /**
         * Save Early Access taxonomy when checkbox is changed
         *
         * @since Brandwoods 2025
         *
         * @param int $post_id The post ID.
         * @return void
         */
        function brandwoods_save_early_access_meta( $post_id ) {
            // Check nonce
            if ( ! isset( $_POST['early_access_nonce'] ) || ! wp_verify_nonce( $_POST['early_access_nonce'], 'early_access_nonce' ) ) {
                return;
            }

            // Check autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check permissions
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            $taxonomy = 'article_early_access';
            
            // Get or create the term
            $term = get_term_by('slug', 'early-access', $taxonomy);
            if (!$term) {
                $result = wp_insert_term('Early Access', $taxonomy, array('slug' => 'early-access'));
                if (!is_wp_error($result)) {
                    $term = get_term($result['term_id'], $taxonomy);
                }
            }

            if ($term && !is_wp_error($term)) {
                $is_checked = isset( $_POST['is_early_access'] ) && $_POST['is_early_access'] === '1';
                
                if ( $is_checked ) {
                    // Add the term
                    wp_set_object_terms( $post_id, array( (int) $term->term_id ), $taxonomy, false );
                } else {
                    // Remove the term
                    wp_remove_object_terms( $post_id, array( (int) $term->term_id ), $taxonomy );
                }
            }
        }
    endif;
    add_action( 'save_post_article', 'brandwoods_save_early_access_meta' );

    // Flush rewrite rules on theme activation
    function brandwoods_flush_rewrite_rules() {
        brandwoods_register_article_post_type();
        brandwoods_register_article_genre_taxonomy();
        brandwoods_register_article_keywords_taxonomy();
        brandwoods_register_article_year_taxonomy();
        brandwoods_register_article_early_access_taxonomy();
        brandwoods_register_issues_post_type();
        brandwoods_register_issue_special_taxonomy();
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
                'has_archive'           => false,
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

    // Register Custom Taxonomy for Issues: Special Issues
    if ( ! function_exists( 'brandwoods_register_issue_special_taxonomy' ) ) :
        /**
         * Register Issue Special Issues Taxonomy
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_register_issue_special_taxonomy() {
            $labels = array(
                'name'              => _x( 'Special Issues', 'Taxonomy General Name', 'brandwoods2025' ),
                'singular_name'     => _x( 'Special Issue', 'Taxonomy Singular Name', 'brandwoods2025' ),
                'menu_name'         => __( 'Special Issues', 'brandwoods2025' ),
                'all_items'         => __( 'All Special Issues', 'brandwoods2025' ),
                'edit_item'         => __( 'Edit Special Issue', 'brandwoods2025' ),
                'update_item'       => __( 'Update Special Issue', 'brandwoods2025' ),
                'add_new_item'      => __( 'Add New Special Issue', 'brandwoods2025' ),
                'new_item_name'     => __( 'New Special Issue Name', 'brandwoods2025' ),
                'search_items'      => __( 'Search Special Issues', 'brandwoods2025' ),
                'not_found'         => __( 'No special issues found', 'brandwoods2025' ),
            );

            $args = array(
                'labels'            => $labels,
                'hierarchical'      => false,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
                'show_in_rest'      => true,
                'rest_base'         => 'issue-special',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
                'rewrite'           => array(
                    'slug'       => 'special-issue',
                    'with_front' => false,
                ),
            );

            register_taxonomy( 'issue_special', array( 'issue' ), $args );
        }
    endif;
    add_action( 'init', 'brandwoods_register_issue_special_taxonomy', 0 );

    // Custom meta box for Special Issue taxonomy (checkbox - flag)
    if ( ! function_exists( 'brandwoods_issue_special_checkbox_meta_box' ) ) :
        /**
         * Custom meta box for Special Issue taxonomy with checkbox
         *
         * @since Brandwoods 2025
         *
         * @param WP_Post $post The post object.
         * @return void
         */
        function brandwoods_issue_special_checkbox_meta_box( $post ) {
            $taxonomy = 'issue_special';
            
            // Get or create the "Special Issue" term
            $term = get_term_by('slug', 'special-issue', $taxonomy);
            if (!$term) {
                $result = wp_insert_term('Special Issue', $taxonomy, array('slug' => 'special-issue'));
                if (!is_wp_error($result)) {
                    $term = get_term($result['term_id'], $taxonomy);
                }
            }
            
            if (!$term || is_wp_error($term)) {
                echo '<p>' . __( 'Error loading Special Issue option.', 'brandwoods2025' ) . '</p>';
                return;
            }

            $current_terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
            $is_checked = in_array($term->term_id, $current_terms);

            wp_nonce_field('special_issue_nonce', 'special_issue_nonce');
            
            echo '<label>';
            echo '<input type="checkbox" name="is_special_issue" value="1" ' . checked($is_checked, true, false) . ' /> ';
            echo __('Mark as Special Issue', 'brandwoods2025');
            echo '</label>';
        }
    endif;

    // Replace the default meta box with custom checkbox meta box for Special Issue
    if ( ! function_exists( 'brandwoods_replace_special_issue_meta_box' ) ) :
        /**
         * Replace the default special issue meta box with custom checkbox version
         *
         * @since Brandwoods 2025
         *
         * @return void
         */
        function brandwoods_replace_special_issue_meta_box() {
            remove_meta_box( 'issue_specialdiv', 'issue', 'side' );
            add_meta_box(
                'issue_special_checkbox',
                __( 'Special Issue', 'brandwoods2025' ),
                'brandwoods_issue_special_checkbox_meta_box',
                'issue',
                'side',
                'high'
            );
        }
    endif;
    add_action( 'add_meta_boxes', 'brandwoods_replace_special_issue_meta_box' );

    // Save Special Issue checkbox
    if ( ! function_exists( 'brandwoods_save_special_issue_meta' ) ) :
        /**
         * Save Special Issue taxonomy when checkbox is changed
         *
         * @since Brandwoods 2025
         *
         * @param int $post_id The post ID.
         * @return void
         */
        function brandwoods_save_special_issue_meta( $post_id ) {
            // Check nonce
            if ( ! isset( $_POST['special_issue_nonce'] ) || ! wp_verify_nonce( $_POST['special_issue_nonce'], 'special_issue_nonce' ) ) {
                return;
            }

            // Check autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check permissions
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            $taxonomy = 'issue_special';
            
            // Get or create the term
            $term = get_term_by('slug', 'special-issue', $taxonomy);
            if (!$term) {
                $result = wp_insert_term('Special Issue', $taxonomy, array('slug' => 'special-issue'));
                if (!is_wp_error($result)) {
                    $term = get_term($result['term_id'], $taxonomy);
                }
            }

            if ($term && !is_wp_error($term)) {
                $is_checked = isset( $_POST['is_special_issue'] ) && $_POST['is_special_issue'] === '1';
                
                if ( $is_checked ) {
                    // Add the term
                    wp_set_object_terms( $post_id, array( (int) $term->term_id ), $taxonomy, false );
                } else {
                    // Remove the term
                    wp_remove_object_terms( $post_id, array( (int) $term->term_id ), $taxonomy );
                }
            }
        }
    endif;
    add_action( 'save_post_issue', 'brandwoods_save_special_issue_meta' );
    
?>