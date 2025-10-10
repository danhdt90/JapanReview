<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
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
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// Register Custom Post Type: Book
if ( ! function_exists( 'twentytwentyfive_register_book_post_type' ) ) :
	/**
	 * Register Book Custom Post Type
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_book_post_type() {
		$labels = array(
			'name'                  => _x( 'Books', 'Post Type General Name', 'twentytwentyfive' ),
			'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'twentytwentyfive' ),
			'menu_name'             => __( 'Books', 'twentytwentyfive' ),
			'name_admin_bar'        => __( 'Book', 'twentytwentyfive' ),
			'archives'              => __( 'Book Archives', 'twentytwentyfive' ),
			'attributes'            => __( 'Book Attributes', 'twentytwentyfive' ),
			'parent_item_colon'     => __( 'Parent Book:', 'twentytwentyfive' ),
			'all_items'             => __( 'All Books', 'twentytwentyfive' ),
			'add_new_item'          => __( 'Add New Book', 'twentytwentyfive' ),
			'add_new'               => __( 'Add New', 'twentytwentyfive' ),
			'new_item'              => __( 'New Book', 'twentytwentyfive' ),
			'edit_item'             => __( 'Edit Book', 'twentytwentyfive' ),
			'update_item'           => __( 'Update Book', 'twentytwentyfive' ),
			'view_item'             => __( 'View Book', 'twentytwentyfive' ),
			'view_items'            => __( 'View Books', 'twentytwentyfive' ),
			'search_items'          => __( 'Search Book', 'twentytwentyfive' ),
			'not_found'             => __( 'Not found', 'twentytwentyfive' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'twentytwentyfive' ),
			'featured_image'        => __( 'Featured Image', 'twentytwentyfive' ),
			'set_featured_image'    => __( 'Set featured image', 'twentytwentyfive' ),
			'remove_featured_image' => __( 'Remove featured image', 'twentytwentyfive' ),
			'use_featured_image'    => __( 'Use as featured image', 'twentytwentyfive' ),
			'insert_into_item'      => __( 'Insert into book', 'twentytwentyfive' ),
			'uploaded_to_this_item' => __( 'Uploaded to this book', 'twentytwentyfive' ),
			'items_list'            => __( 'Books list', 'twentytwentyfive' ),
			'items_list_navigation' => __( 'Books list navigation', 'twentytwentyfive' ),
			'filter_items_list'     => __( 'Filter books list', 'twentytwentyfive' ),
		);

		$args = array(
			'label'                 => __( 'Book', 'twentytwentyfive' ),
			'description'           => __( 'A custom post type for books', 'twentytwentyfive' ),
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
			'rest_base'             => 'books',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rewrite'               => array(
				'slug'       => 'books',
				'with_front' => false,
			),
		);

		register_post_type( 'book', $args );
	}
endif;
add_action( 'init', 'twentytwentyfive_register_book_post_type', 0 );

// Register Custom Taxonomy for Books: Genre
if ( ! function_exists( 'twentytwentyfive_register_book_genre_taxonomy' ) ) :
	/**
	 * Register Book Genre Taxonomy
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_book_genre_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Genres', 'Taxonomy General Name', 'twentytwentyfive' ),
			'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'twentytwentyfive' ),
			'menu_name'                  => __( 'Genres', 'twentytwentyfive' ),
			'all_items'                  => __( 'All Genres', 'twentytwentyfive' ),
			'parent_item'                => __( 'Parent Genre', 'twentytwentyfive' ),
			'parent_item_colon'          => __( 'Parent Genre:', 'twentytwentyfive' ),
			'new_item_name'              => __( 'New Genre Name', 'twentytwentyfive' ),
			'add_new_item'               => __( 'Add New Genre', 'twentytwentyfive' ),
			'edit_item'                  => __( 'Edit Genre', 'twentytwentyfive' ),
			'update_item'                => __( 'Update Genre', 'twentytwentyfive' ),
			'view_item'                  => __( 'View Genre', 'twentytwentyfive' ),
			'separate_items_with_commas' => __( 'Separate genres with commas', 'twentytwentyfive' ),
			'add_or_remove_items'        => __( 'Add or remove genres', 'twentytwentyfive' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'twentytwentyfive' ),
			'popular_items'              => __( 'Popular Genres', 'twentytwentyfive' ),
			'search_items'               => __( 'Search Genres', 'twentytwentyfive' ),
			'not_found'                  => __( 'Not Found', 'twentytwentyfive' ),
			'no_terms'                   => __( 'No genres', 'twentytwentyfive' ),
			'items_list'                 => __( 'Genres list', 'twentytwentyfive' ),
			'items_list_navigation'      => __( 'Genres list navigation', 'twentytwentyfive' ),
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
			'rest_base'                  => 'book-genres',
			'rest_controller_class'      => 'WP_REST_Terms_Controller',
			'rewrite'                    => array(
				'slug'       => 'book-genre',
				'with_front' => false,
			),
		);

		register_taxonomy( 'book_genre', array( 'book' ), $args );
	}
endif;
add_action( 'init', 'twentytwentyfive_register_book_genre_taxonomy', 0 );

// Flush rewrite rules on theme activation
function twentytwentyfive_flush_rewrite_rules() {
	twentytwentyfive_register_book_post_type();
	twentytwentyfive_register_book_genre_taxonomy();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'twentytwentyfive_flush_rewrite_rules' );
