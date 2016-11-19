<?php

if ( ! function_exists( 'fjtg_shortcode_icon' ) ) {
	function fjtg_shortcode_icon( $params, $content = null ) {
		foreach ( $params as $key => $value ) {
			if ( is_int( $key ) && !empty($value)) {
				$icon_name = $value;
			}
		}
		if ( !empty( $icon_name ) ) {
			return "<i class='fa fa-" . $icon_name . "'></i>";
		}
	}
}
add_shortcode('i', 'fjtg_shortcode_icon');


if ( ! function_exists( 'fjtg_shortcode_break' ) ) {
	function fjtg_shortcode_break() {
		return "<br/>";
	}
}
add_shortcode('br', 'fjtg_shortcode_break');


if ( ! function_exists( 'fjtg_shortcode_serif' ) ) {
	function fjtg_shortcode_serif( $params, $content = null ) {
		extract(shortcode_atts(array(
			// 'title' => null,
		), $params));

		// By default, just copy the content
		$output = $content;

		// Add html if language uses alphabet
		if ( !rojak_str_contains( ICL_LANGUAGE_CODE, 'zh' ) ||
			   !rojak_str_contains( ICL_LANGUAGE_CODE, 'ja' ) ) {
			$output  = "<span class='u_serif'>";
			$output .= $content;
			$output .= '</span>';
		}

		return $output;
	}
}
add_shortcode('serif', 'fjtg_shortcode_serif');


if ( ! function_exists( 'fjtg_shortcode_jotform_newsletter' ) ) {
	function fjtg_shortcode_jotform_newsletter( $params, $content = null ) {
		extract(shortcode_atts(array(
			// 'title' => null,
		), $params));

		$jotform_args = array(
			'id'     => '61783028104451',
			'name'   => 'newsletter',
			'width'  => '100%',
			'height' => '130px',
		);

		$text_loading = __( 'Loading Sign Up Form&hellip;', 'fjtg' );

		$output  = fjtg_jotform( $jotform_args );
		$output .= <<<HTML
<p class="footer-newsletter__loading js_newsletter__loading">
	$text_loading
</p>
<div class="footer-newsletter__jotform js_newsletter__jotform"></div>
HTML;

		return $output;
	}
}
add_shortcode('jotform_newsletter', 'fjtg_shortcode_jotform_newsletter');

if ( !function_exists('fjtg_column_in_content') ) {
	function fjtg_column_in_content( $attr, $content = '' ) {
		$output = "<div class='two-col'>$content</div>";

		return $output;
	}
}
add_shortcode('two-columns', 'fjtg_column_in_content' );

if ( !function_exists('fjtg_half_column_in_content') ) {
	function fjtg_half_column_in_content( $attr, $content = '' ) {
		$output = "<div class='col-md-2'>$content</div>";

		return $output;
	}
}
add_shortcode('column-half', 'fjtg_half_column_in_content' );