<?php

/**
 * Google Map URL generator
 */
if ( ! function_exists( 'rojak_get_googlemap_url' ) ) {
	function rojak_get_googlemap_url( $args = array(), $base_url = 'https://maps.googleapis.com/maps/api/js'  ) {

		$defaults = array(
			'v'        => '3',
			'language' => ICL_LANGUAGE_CODE,
			'key'      => 'AIzaSyAp16-BNgTll5F3xOW6fuClAbjP0qe4pQo'
		);
		$args = wp_parse_args( $args, $defaults );

		// Another layer for add_filter to update $args
		$filter_args = apply_filters( 'rojak_googlemap_args', false );
		$args = wp_parse_args( $args, $filter_args );

		$base_url = esc_url( $base_url ) . '?' . http_build_query( $args );
		return $base_url;

	}
}
