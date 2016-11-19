<?php
/**
 * Functions for handling WPML related features
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Returns the default language post ID
 *
 * @since  0.9.0
 * @access public
 * @param  int     $post_id
 * @param  string  $post_type
 * @return int
 */
function rojak_get_primary_lang_post_id( $post_id, $post_type = 'page' ) {

	global $sitepress;
	$default_language = $sitepress->get_default_language();

	if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE != $default_language ) {
		return apply_filters( 'wpml_object_id', $post_id, $post_type, true, $default_language );
	} else {
		return $post_id;
	}

}
