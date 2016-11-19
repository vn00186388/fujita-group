<?php

add_action( 'wp_update_nav_menu', 'fjtg_menu_generateHTMLByID' );

/**
 * Action when a menu is saved
 * HTML generator for all the Menus
 */
function fjtg_menu_generateHTMLByID( $menu_id ) {

	$menu = wp_get_nav_menu_object( $menu_id );

	$locations = get_nav_menu_locations();
	$menu_location = null;
	foreach ($locations as $key => $value) {
		if ( $menu->term_id == $value ) {
			$menu_location = $key;
			break;
		}
	}

	fjtg_menu_generateHTMLByLocation( $menu_location );
}


/**
 * HTML generator for all the Menus
 */
function fjtg_menu_generateHTMLByLocation( $menu_location = null ) {

	if ( empty( $menu_location ) ) {
		return;
	}

	$lang_code = ICL_LANGUAGE_CODE;
	$menu_args = array(
		'theme_location' => $menu_location,
		'menu_class'     => "menu__ul menu--{$menu_location}__ul menu__ul--{$lang_code}",
		'container_class'=> "menu-{$menu_location}-container",
		'fallback_cb'    => false,
		'echo'           => false,
	);

	if ( rojak_str_contains( $menu_location, 'footer-nav-' ) ) {

		$menu_args['menu_class'] = "menu--footer__ul menu--{$menu_location}__ul";

		if ( 'footer-nav-one' == $menu_location && class_exists( 'Hv_Walker_Footer_Nav_One' ) ) {
			$footer_nav_one = new Fjtg_Walker_Footer_Nav_One;
			$menu_args['walker'] = $footer_nav_one;
		}

		else if ( 'footer-nav-three' == $menu_location  && class_exists( 'Hv_Walker_Footer_Nav_Last' ) ) {
			$footer_nav_five = new Fjtg_Walker_Footer_Nav_Last;
			$menu_args['walker'] = $footer_nav_five;
		}

	}

	$html = wp_nav_menu( $menu_args );

	$file_path = fjtg_menu_getFilePath( $menu_location );
	rojak_write_to_file( $file_path, $html );

}


/**
 * Get file path
 */
function fjtg_menu_getFilePath( $menu_location ) {
	$lang_code = ICL_LANGUAGE_CODE;
	$file_ext  = "menu-{$menu_location}-{$lang_code}.html";
	return rojak_get_json_site_dir() . $file_ext;
}


/**
 * Display Menu
 */
function fjtg_menu_display( $menu_location = null ) {
	if ( !empty( $menu_location ) ) {
		$html_file = fjtg_menu_getFilePath( $menu_location );

		if ( ! is_file ( $html_file ) ) {
			fjtg_menu_generateHTMLByLocation( $menu_location );
		}

		echo file_get_contents( $html_file );
	}
	return false;
}