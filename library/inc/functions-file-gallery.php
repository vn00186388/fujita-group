<?php
/**
 * Functions for handling File Gallery attachments
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Returns the post attachments
 *
 * @since  0.9.0
 * @access public
 * @param  int     $post_id
 * @param  string  $post_type
 * @return object
 */
function rojak_get_post_attachments( $post_id, $post_type = 'page' ) {

	$args = array(
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_type'      => 'attachment',
		'post_parent'    => rojak_get_primary_lang_post_id( $post_id, $post_type ),
		'post_mime_type' => 'image',
		'post_status'    => null
	);
	$attachments = get_posts( $args );

	return $attachments;
}

/**
 * Whether post has attachments with the specified media tag
 *
 * @since  0.9.0
 * @access public
 * @param  int     $post_id
 * @param  string  $media_tag
 * @param  string  $post_type
 * @return bool
 */
function rojak_has_page_attachments( $post_id, $media_tag = '', $post_type = 'page' ) {

	$args = array(
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_type'      => 'attachment',
		'post_parent'    => rojak_get_primary_lang_post_id( $post_id, $post_type ),
		'post_mime_type' => 'image',
		'post_status'    => null
	);
	$attachments = get_posts( $args );

	if ( ! empty( $media_tag ) ) {
		$term_found = false;
		foreach ( $attachments as $attachment ) {
			$attachment_id = $attachment->ID;
			$term_list     = wp_get_post_terms( $attachment_id, 'media_tag', array( 'fields' => 'all' ) );
			$terms         = array();
			foreach ( $term_list as $term ) {
				array_push($terms, $term->slug);
			}

			if ( in_array( $media_tag, $terms ) ) {
				return true;
				break;
			}
		}
	} else {
		if ( $attachments ) {
			return true;
		}
	}

	return false;
}

