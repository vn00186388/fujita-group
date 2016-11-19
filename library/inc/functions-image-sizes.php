<?php
/**
 * Images Sizes
 *
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function rojak_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}


/**
 * Get size information for a specific image size.
 *
 * @uses   rojak_get_image_sizes()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function rojak_get_image_size( $size ) {
	$sizes = rojak_get_image_sizes();

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}


/**
 * Get featured image
 *
 * @uses   rojak_get_featured_image()
 */
function rojak_get_featured_image( $post_id, $post_type ) {
	$image_info = wp_prepare_attachment_for_js(
		get_post_thumbnail_id(
			rojak_get_primary_lang_post_id(
				$post_id ,
				$post_type
			)
		)
	);
	if ( ! rojak_empty_array( $image_info ) ) {
		return $image_info;
	}
	return false;
}


/**
 * Get placeholder image
 *
 * @uses   rojak_get_placeholder_image()
 */
function rojak_get_placeholder_image( $args = array() ) {

	$defaults = array(
		'txtsize'  => 30,
		'txt'      => __( 'Holiday Villa', 'fjtg' ),
		'w'        => 200,
		'h'        => 200,
		'bg'       => null,
	);

	$args = wp_parse_args( $args, $defaults );

	$url = "https://placeholdit.imgix.net/~text";
	return esc_url( $url ) . '?' . http_build_query( $args );
}