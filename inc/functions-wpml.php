<?php

/**
 * WPML Remove CSS and JS
 *
 * @link http://notboring.org/devblog/2012/08/how-to-remove-the-embedded-sitepress-multilingual-cmsrescsslanguage-selector-css-from-your-own-wordpress-templates-on-wpml-installations/
 */
if ( ! function_exists( 'fjtg_wpml_dont_load' ) ) {
	function fjtg_wpml_dont_load() {
		define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
		define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
		define('ICL_DONT_LOAD_LANGUAGES_JS', true);
	}
}
add_action( 'init', 'fjtg_wpml_dont_load' );


/**
 * Add wpml inline script in the footer
 */
if ( ! function_exists( 'fjtg_wpml_data' ) ) {
	function fjtg_wpml_data() {
		global $sitepress;
		$vars = array(
			'current_language' => ICL_LANGUAGE_CODE,
			'icl_home'         => $sitepress->language_url(),
			'ajax_url'         => admin_url('admin-ajax.php'),
		);
		$html = json_encode( $vars );
		echo "<script>var icl_vars = $html;</script>\n";
	}
}
add_action('wp_footer', 'fjtg_wpml_data');


/**
 * WPML custom HREFLANG for hotel page
 */
function fjtg_wpml_alternate_hreflang($lang_url, $ICL_LANGUAGE_CODE) {
	global $FB_CHENDOL;
	if(!empty($FB_CHENDOL->current_hotel)) {
		$lang_url .= $FB_CHENDOL->current_hotel . '/';
	}
	if(!empty($FB_CHENDOL->current_page_slug)) {
		$lang_url .= $FB_CHENDOL->current_page_slug . '/';
	}
	return $lang_url;
}
add_filter('wpml_alternate_hreflang', 'fjtg_wpml_alternate_hreflang', 10, 2);