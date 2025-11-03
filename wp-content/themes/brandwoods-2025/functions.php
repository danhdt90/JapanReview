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
            get_template_directory_uri() . 'assets/css/style-be.css',
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

    
?>