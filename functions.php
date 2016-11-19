<?php
/**
 * fjtg functions and definitions
 *
 * @package fjtg
 */


// Launch the Rojak framework.
require_once( trailingslashit( get_template_directory() ) . 'library/rojak.php' );
new Rojak();

add_action( 'after_setup_theme', 'fjtg_setup' );

if ( ! function_exists( 'fjtg_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function fjtg_setup() {

	add_theme_support( 'rojak-templates' );

	add_theme_support( 'rojak-assets-async' );

	add_theme_support( 'rojak-assets-timestamp' );

	// Running gulp --prod will uncomment below
	//--DEV--add_theme_support( 'rojak-templates-minify' );

	// Automatically add <title> to head.
	add_theme_support( 'title-tag' );

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on fjtg, use a find and replace
	 * to change 'fjtg' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'fjtg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => 'Primary Menu',
	) );

	register_nav_menus( array(
		'global' => 'Global Menu',
	) );

	register_nav_menus( array(
		'footer' => 'Footer Menu',
	) );


  // - Get Connected Menu: Title / Description
  // - Social Menu: Facebook, Twitter, Youtube, Instagram
  // - Contact Menu: Address, Phone (6-03) 2162-2922, Fax (6-03) 2162-2937, Email
  // - Newsletter Menu: Description / Form
  // - Footer Menu: About Us, Contact Us, Management Server, News & Events, Careers, Global, etc


	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'fjtg_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
		/*
		 * Enable excerpt on Pages
		 */
		add_post_type_support('page', 'excerpt');
}
endif; // fjtg_setup


/**
 * Remove Post Menu in Admin.
 */
if ( ! function_exists( 'fjtg_admin_remove_menu' ) ) {
	function fjtg_admin_remove_menu() {
		remove_menu_page( 'edit.php' );
	}
}
add_action( 'admin_menu', 'fjtg_admin_remove_menu' );


/**
 * Add SVG
 */
if (!function_exists('fjtg_cc_mime_types')) {
	function fjtg_cc_mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}
add_filter('upload_mimes', 'fjtg_cc_mime_types');


/**
 * Disable emoji feature introduced in Wordpresss 4.2
 */
if (!function_exists('fjtg_disable_emojicons_tinymce')) {
	function fjtg_disable_emojicons_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
}

if (!function_exists('fjtg_disable_wp_emojicons')) {
	function fjtg_disable_wp_emojicons() {

		// all actions related to emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		// filter to remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', 'fjtg_disable_emojicons_tinymce' );
	}
}
add_action( 'init', 'fjtg_disable_wp_emojicons' );


/**
 * Disable Admin Bar
 */
if (!function_exists('fjtg_admin_bar')) {
	function fjtg_admin_bar() {
		add_filter('show_admin_bar', '__return_false');
	}
}
// add_action( 'init', 'fjtg_admin_bar' );


/**
 * Add specific CSS class by filter
 */
if (!function_exists('fjtg_body_class_names')) {
	function fjtg_body_class_names( $classes ) {
		// Set hotel name in body
		$hotel_name = get_bloginfo( 'name' );
		if ( empty( $hotel_name ) ) {
			$hotel_name = get_current_blog_id();
		}
		$classes[] = preg_replace('/\s+/', '-', strtolower( $hotel_name ) );

		if( preg_match('/(?i)msie 9/',$_SERVER['HTTP_USER_AGENT']) ) {
			array_push( $classes, 'ie-9' );
		}

		return $classes;
	}
}
add_filter( 'body_class', 'fjtg_body_class_names' );


/**
 * Load string translations
 */
if ( ! function_exists( 'fjtg_load_textdomain' ) ) {
	function fjtg_load_textdomain()  {
		global $sitepress;
		$locale='en_US';
		if ( $sitepress ) {
			$locale = $sitepress->get_locale(ICL_LANGUAGE_CODE);
		}
		$child_mo = ROJAK_CHILD . 'languages/' . $locale . '.mo';
		if ( file_exists( $child_mo ) ) {
			load_theme_textdomain( 'fjtg', ROJAK_CHILD . 'languages/');
		} else {
			load_theme_textdomain( 'fjtg', ROJAK_PARENT . 'languages/');
		}
	}
}
add_action('after_setup_theme', 'fjtg_load_textdomain');


if ( ! function_exists( 'fjtg_json_dir' ) ) {
	function fjtg_json_dir() {
		// set to same dir as group/subsite site
		return trailingslashit( 'fujita-json' );
	}
}
add_filter('rojak_json_dir',  'fjtg_json_dir');


/**
 * Add Base CSS
 */
if ( ! function_exists( 'fjtg_enqueue_base_css' ) ) {
	function fjtg_enqueue_base_css() {
		$brand       = fjtg_get_current_brand();
		$brand_color = $brand[ 'brand_color_scheme' ];
		$base_custom = $brand_color ? "base-$brand_color" : 'base';
		$style_name  = $base_custom . $GLOBALS['rojak_templates_minify'] . '.css';

		wp_enqueue_style( 'base', ROJAK_PARENT_URI . $style_name );
		do_action('fjtg_after_base_css');
	}
}
add_action( 'rojak_tpl_after_core_css', 'fjtg_enqueue_base_css' );


/**
 * Image sizes
 */
if (!function_exists('fjtg_image_sizes')) {
	function fjtg_image_sizes() {

		// add_image_size( 'size-name', 220, 180, true );
		// 220 pixels wide by 180 pixels tall, crop image

		add_image_size( 'small',               '80',   '80',   true );
		add_image_size( 'slider',              '1600', '800',  true );
		add_image_size( 'slider-uncrop',       '1600', '800',  false );
		add_image_size( 'slider-medium',       '960',  '480',  true );
		add_image_size( 'slider-thumb',        '160',  '80',   true );
		add_image_size( 'slider-tablet',       '1024', '512',  true );
		add_image_size( 'slider-mobile',       '600',  '300',  true );

	}
}
add_action( 'init', 'fjtg_image_sizes' );

/**
 * on save functions
 * these will generate json or html files
 */
require_once get_template_directory() . '/inc/save-menu.php';
require_once get_template_directory() . '/inc/save-post-brand.php';
require_once get_template_directory() . '/inc/save-post-city.php';
require_once get_template_directory() . '/inc/save-post-facility.php';
require_once get_template_directory() . '/inc/save-post-hotel.php';
// require_once get_template_directory() . '/inc/save-post-tpl-home.php';

/**
 * wp_footer hooks
 */
// require_once get_template_directory() . '/inc/functions-wp-footer.php';

/**
 * WPML Hooks
 */
// require_once get_template_directory() . '/inc/functions-wpml.php';

/**
 * fjtg-admin
 */
require_once get_template_directory() . '/inc/fjtg-admin.php';

/**
 * Custom Public Methods
 */
require_once get_template_directory() . '/inc/fjtg-utilities.php';
// require_once get_template_directory() . '/inc/class-fjtg-walker-footer-nav-one.php';
// require_once get_template_directory() . '/inc/class-fjtg-walker-footer-nav-last.php';

/**
 * Load External Classes
 */
// require_once get_template_directory() . '/inc/class-mobile-detect.php';

/**
 * Load Breadcrumb function
 */
// require_once get_template_directory() . '/inc/fjtg-breadcrumb.php';

/**
 * Load shortcodes
 */
// require_once get_template_directory() . '/inc/fjtg-shortcodes.php';

/**
 * Metabox
 */
// require_once get_template_directory() . '/inc/fjtg-metabox.php';

/**
 * Custom REST API
 */
require_once get_template_directory() . '/inc/fjtg-rest.php';
