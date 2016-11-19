<?php
/**
 * Functions for handling Fastbooking offers.
 * From starting-from, offers, promotions.
 *
 * @package    Rojak
 * @subpackage Includes
 * @author     Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright  Copyright (c) 2016, Fastbooking
 * @link
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Outputs the URL for Fastbooking's widget.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $args
 * @return void
 */
function rojak_fb_widget_url( $args = array() ) {
	echo rojak_get_fb_widget_url( $args );
}

/**
 * Returns the URL for Fastbooking's widget.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $args
 * @return string
 */
function rojak_get_fb_widget_url( $args = array() ) {

	global $sitepress;
	$language = $sitepress ? ICL_LANGUAGE_CODE : 'en';
	if ($language == 'pt-pt' || $language == 'pt-br'){
		$language = 'pt';
	} elseif ($language == 'zh-hans'){
		$language = 'zh_Hans_CN';
	} elseif ($language == 'zh-hant'){
		$language = 'zh_Hant_HK';
	}

	$defaults = array(
		'snippet'  => 'promotionorderable',
		'divdest'  => null,
		'lg'       => $language,
		'nb'       => 1,
		'cta'      => __( 'Check Availability', 'rojak' ),
		'ctam'     => __( 'More info', 'rojak' ),
		'apd'      => __( 'From',      'rojak' ),
		'pn'       => '',
		'js_flag'  => 1,
		'pb_flag'  => 1,
		'gold'     => 1,
    'round'    => 1,
		'paragraph'=> 0,
		'format'   => '0;,;,',
		'orderby'  => 'price',
		'order'    => 'asc',
		'displayOrder' => 'tpbm-tpidb',
	);
	$args = wp_parse_args( $args, $defaults );

	// Another layer for add_filter to update $args
	$filter_args = apply_filters( 'rojak_fb_widget_args', false );
	$args = wp_parse_args( $args, $filter_args );

	$default_base_url = "http://hotelsitecontents.fastbooking.com/router.php";
	$base_url 				= apply_filters( 'rojak_fb_widget_base_url', $default_base_url );

	$base_url = esc_url( $base_url ) . '?' . http_build_query( $args );

	return 'FB.CrossCom.consume( "' . $base_url . '", "' . $args['divdest'] . '" )' . "\n";

}
