<?php
/**
 * Functions for handling content related information
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Limit the number of words returned in a given string
 *
 * @since  0.9.0
 * @access public
 * @param  string $string
 * @param  int    $count
 * @return void
 */
function rojak_get_first_words($string, $count = 20 ) {
	if (ICL_LANGUAGE_CODE !== 'en') {
		return rojak_get_first_chars($string, $count * 4 );
	}

	preg_match( "~(?:\w+(?:\W+|$)){0,$count}~", html_entity_decode(strip_tags($string)), $matches);
	return rtrim($matches[0]).(str_word_count($string) >= $count - 1 ? '&hellip;' : '');
}

/**
 * Limit the number of words returned in a given string
 *
 * @since  0.9.0
 * @access public
 * @param  string $string
 * @param  int    $count
 * @return void
 */
function rojak_get_first_chars($string, $count = 100 ) {
	return mb_substr(strip_tags($string), 0, $count).(mb_strlen($string) >= ($count - 1) ? '&hellip;' : '');
}

/**
 * Returns the excerpt with given number of words.
 *
 * @since  0.9.0
 * @access public
 * @param  string $post_excerpt
 * @param  string $post_content
 * @param  int    $length
 * @return string|html
 */
function rojak_get_excerpt( $post_excerpt, $post_content, $length = 22 ) {
	$entry_excerpt = apply_filters('the_excerpt', $post_excerpt);
	if ( empty( $entry_excerpt ) ) {
		$entry_excerpt = apply_filters( 'the_content', $post_content );
	}
	// $entry_excerpt_cut = fb_limit_words( $entry_excerpt, $length );
	$entry_excerpt_cut = rojak_get_first_words( $entry_excerpt, $length );
	if ( !empty( $entry_excerpt_cut ) ) {
		$entry_excerpt = apply_filters( 'the_content', $entry_excerpt_cut );
	}
	return $entry_excerpt;
}