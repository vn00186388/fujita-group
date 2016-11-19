<?php

if ( current_theme_supports( 'rojak-templates-minify' ) ) {
	$GLOBALS['rojak_templates_minify'] = '.min';
} else {
	$GLOBALS['rojak_templates_minify'] = '';
}

add_action( 'wp_enqueue_scripts', 'rojak_tpl_core_assets' );
add_action( 'wp_enqueue_scripts', 'rojak_tpl_page_assets' );
add_action( 'wp',                 'rojak_tpl_page_fn'     );
add_filter( 'single_template',    'rojak_tpl_single'      );
add_filter( 'archive_template',   'rojak_tpl_archive'     );


function rojak_tpl_core_assets() {
	rojak_tpl_core_css();
	rojak_tpl_core_js();
}

/**
 * Base scripts
 */
function rojak_tpl_core_js() {

	do_action('rojak_tpl_before_jquery_js');
	wp_dequeue_script(    'jquery' );
	wp_deregister_script( 'jquery' );
	wp_register_script(   'jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery' .$GLOBALS['rojak_templates_minify']. '.js', array(), '1.11.1', true );
	wp_enqueue_script(    'jquery' );
	do_action('rojak_tpl_after_jquery_js');

	do_action('rojak_tpl_before_core_js');
	wp_register_script( 'rojak', ROJAK_PARENT_URI . 'js/core' .$GLOBALS['rojak_templates_minify']. '.js', array(), '', true );
	wp_enqueue_script(  'rojak' );
	do_action('rojak_tpl_after_core_js');

}

/**
 * Base stylesheet
 */
function rojak_tpl_core_css() {
	$style_name = 'core' . $GLOBALS['rojak_templates_minify'] . '.css';
	do_action('rojak_tpl_before_core_css');
	wp_enqueue_style( 'rojak', ROJAK_PARENT_URI . $style_name );
	do_action('rojak_tpl_after_core_css');
}


/**
 * Custom stylesheet for child themes
 */
function rojak_tpl_core_custom_css() {
	$custom_css      = 'custom.css';
	$custom_css_path = ROJAK_CHILD . $custom_css;
	if ( file_exists( $custom_css_path ) && is_child_theme() ) {
		wp_enqueue_style( 'rojak-custom', ROJAK_CHILD_URI . $custom_css );
	}
}
add_action( 'rojak_tpl_after_core_css', 'rojak_tpl_core_custom_css' );


/**
 * Custom javascript for child themes
 */
function rojak_tpl_core_custom_js() {
	$custom_js      = 'custom.js';
	$custom_js_path = ROJAK_CHILD . $custom_js;
	if ( file_exists( $custom_js_path ) && is_child_theme() ) {
		wp_enqueue_script( 'rojak-custom', ROJAK_CHILD_URI . $custom_js, array(), '', true  );
	}
}
add_action( 'rojak_tpl_after_core_js', 'rojak_tpl_core_custom_js' );


/**
 * Base assets for page templates and single templates
 */
function rojak_tpl_page_assets() {
	global $post;
	if ( is_page() ) {
		if ( !get_page_template_slug( $post->ID ) ) {
			// If page is using default template
			// Do nothing :3
		} else {
			// If page is using a page template
			// Page template convention in rojak is tpl-name/tpl-name
			$current_tpl       = get_post_meta( $post->ID, '_wp_page_template', true );
			$current_tpl_parts = pathinfo( $current_tpl );
			$current_tpl_name  = $current_tpl_parts['dirname'];
			$current_tpl_path  = ROJAK_PARENT . $current_tpl;

			if ( is_file( $current_tpl_path ) ) {
				$parts = pathinfo( $current_tpl_path );
				$curret_base_path = $parts['dirname'] . '/' . $parts['filename'];
				rojak_tpl_page_assets_queue( $current_tpl_name, $curret_base_path );
			}
		}
	} else if ( is_single() || is_archive() ) {
		if ( is_single() ) {
			// If page is single, convention in rojak is tpl-single-cpt/tpl-single-cpt
			$tpl_name = 'tpl-single-' . $post->post_type;
		} else if ( is_archive() ) {
			// If page is archive, convention in rojak is tpl-archive-cpt/tpl-archive-cpt
			$tpl_name = 'tpl-archive-' . $post->post_type;
		}

		$tpl_path = ROJAK_PARENT . rojak_tpl_get_path( $tpl_name, 'php' );

		if ( is_file( $tpl_path ) ) {
			$tpl_base_path = ROJAK_PARENT . $tpl_name . '/' . $tpl_name;
			rojak_tpl_page_assets_queue( $tpl_name, $tpl_base_path );
		}
	}
}

function rojak_tpl_page_assets_queue( $name, $path ) {
	if ( !empty( $name ) && !empty( $path ) ) {
		// rojak_tpl_require_tpl_fn( $name );
		rojak_tpl_page_css( $name, $path );
		rojak_tpl_page_js( $name, $path );
		rojak_tpl_page_react_js( $name, $path );
		rojak_tpl_page_custom( $name );
	}
}

function rojak_tpl_page_fn() {
	global $post;
	if ( is_page() ) {
		if ( !get_page_template_slug( $post->ID ) ) {
			// If page is using default template
			// Do nothing :3
		} else {
			// If page is using a page template
			// Page template convention in rojak is tpl-name/tpl-name
			$current_tpl       = get_post_meta( $post->ID, '_wp_page_template', true );
			$current_tpl_parts = pathinfo( $current_tpl );
			$current_tpl_name  = $current_tpl_parts['dirname'];
			$current_tpl_path  = ROJAK_PARENT . $current_tpl;

			if ( is_file( $current_tpl_path ) ) {
				rojak_tpl_require_tpl_fn( $current_tpl_name );
			}
		}
	} else if ( is_single() || is_archive() ) {
		if ( is_single() ) {
			// If page is single, convention in rojak is tpl-single-cpt/tpl-single-cpt
			$tpl_name = 'tpl-single-' . $post->post_type;
		} else if ( is_archive() ) {
			// If page is archive, convention in rojak is tpl-archive-cpt/tpl-archive-cpt
			$tpl_name = 'tpl-archive-' . $post->post_type;
		}

		$tpl_path = ROJAK_PARENT . rojak_tpl_get_path( $tpl_name, 'php' );

		if ( is_file( $tpl_path ) ) {
			$tpl_base_path = ROJAK_PARENT . $tpl_name . '/' . $tpl_name;
			rojak_tpl_require_tpl_fn( $tpl_name );
		}
	}
}


function rojak_tpl_page_css( $name, $path ) {
	if ( is_file( $path . $GLOBALS['rojak_templates_minify'] . '.css' ) ) {
		do_action('rojak_tpl_before_page_css');
		wp_enqueue_style( $name, ROJAK_PARENT_URI . rojak_tpl_get_path( $name, $GLOBALS['rojak_templates_minify'] . '.css' ) );
		do_action('rojak_tpl_after_page_css');
	}
}

function rojak_tpl_page_js( $name, $path ) {
	if ( is_file( $path . $GLOBALS['rojak_templates_minify'] . '.js' ) ) {
		do_action('rojak_tpl_before_page_js');
		wp_enqueue_script( $name, ROJAK_PARENT_URI . rojak_tpl_get_path( $name, $GLOBALS['rojak_templates_minify'] . '.js' ), array(), '', true  );
		do_action('rojak_tpl_after_page_js');
	}
}

function rojak_tpl_page_react_js( $name, $path ) {
	$react_bundle     = 'react-bundle';
	$react_bundle_ext = "-{$react_bundle}.js";
	if ( is_file( $path . $react_bundle_ext ) ) {
		// [jes]: injecting react and react dom separately creates
		// issue with included react components
		// will investigate further
		// -------------------------------------------------------------
		// do_action('rojak_tpl_before_react_js');
		// wp_dequeue_script(    'react' );
		// wp_deregister_script( 'react' );
		// wp_register_script(   'react', '//cdnjs.cloudflare.com/ajax/libs/react/15.2.1/react' .$GLOBALS['rojak_templates_minify']. '.js', array(), '15.2.1', true );
		// wp_enqueue_script(    'react' );
		// do_action('rojak_tpl_after_react_js');

		// do_action('rojak_tpl_before_react_dom_js');
		// wp_dequeue_script(    'react-dom' );
		// wp_deregister_script( 'react-dom' );
		// wp_register_script(   'react-dom', '//cdnjs.cloudflare.com/ajax/libs/react/15.2.1/react-dom' .$GLOBALS['rojak_templates_minify']. '.js', array(), '15.2.1', true );
		// wp_enqueue_script(    'react-dom' );
		// do_action('rojak_tpl_after_react_dom_js');

		do_action('rojak_tpl_before_page_react_bundle_js');
		wp_enqueue_script( $name . $react_bundle, ROJAK_PARENT_URI . rojak_tpl_get_path( $name, $react_bundle_ext ), array(), '', true  );
		do_action('rojak_tpl_after_page_react_bundle_js');
	}
}


/**
 * Custom assets for page templates and single templates
 */
function rojak_tpl_page_custom( $name ) {

	$tpl_css      = rojak_tpl_get_path( $name, '.css' );
	$tpl_css_path = ROJAK_CHILD . $tpl_css;
	if ( file_exists( $tpl_css_path ) && is_child_theme() ) {
		wp_enqueue_style( $name . '-custom', ROJAK_CHILD_URI . $tpl_css );
	}

	$tpl_js      = rojak_tpl_get_path( $name, '.js' );
	$tpl_js_path = ROJAK_CHILD . $tpl_js;
	if ( file_exists( $tpl_js_path ) && is_child_theme() ) {
		wp_enqueue_script( $name . '-custom', ROJAK_CHILD_URI . $tpl_js, array(), '', true   );
	}

}


function rojak_tpl_require_tpl_fn( $name ) {

	$tpl_fn_file = ROJAK_PARENT . rojak_tpl_get_path( $name, '-fn.php' );
	if ( file_exists( $tpl_fn_file ) ) {
		require_once( $tpl_fn_file );
	}

}

function rojak_tpl_get_path( $name, $ext ) {

	if ( substr( $ext, 0, 1 ) === '-' ||
			 substr( $ext, 0, 1 ) === '.' ) {
		$path = trailingslashit( $name ) . $name . $ext;
	} else {
		$path = trailingslashit( $name ) . $name . '.' . $ext;
	}

	return $path;

}

function rojak_tpl_single( $single_template ) {
	if ( is_single() ) {
		global $post;
		$single_tpl_name = 'tpl-single-' . $post->post_type;
		$single_tpl_path = ROJAK_PARENT . rojak_tpl_get_path( $single_tpl_name, 'php' );
		if ( is_file( $single_tpl_path ) ) {
			return $single_tpl_path;
		}
	}
	return $single_template;
}

function rojak_tpl_archive( $archive_template ) {
	if ( is_archive() ) {
		global $post;
		$archive_tpl_name = 'tpl-archive-' . $post->post_type;
		$archive_tpl_path = ROJAK_PARENT . rojak_tpl_get_path( $archive_tpl_name, 'php' );
		if ( is_file( $archive_tpl_path ) ) {
			return $archive_tpl_path;
		}
	}
	return $archive_template;
}
