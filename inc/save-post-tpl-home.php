<?php

add_action( 'save_post', 'fjtg_tplHome_OnSave', 10, 2 );

/**
 * Action when homepage is saved
 */
function fjtg_tplHome_OnSave( $post_id, $post) {

	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) )
		return;

	$home_id = get_option( 'page_on_front' );
	if ( $home_id == $post_id || $post->page_template == rojak_tpl_get_path( 'tpl-global-offer', 'php' ) ) {
		fjtg_json_generate_tpl_home();
	}

}

/**
 * JSON generator for all the data needed in Homepage
 */
function fjtg_json_generate_tpl_home() {

	$home_all_data  =  array(
		'slideshow'     => fjtg_tplHome_slideshowData(),
		'global-offers' => fjtg_tplHome_globalOffersData(),
		'places'        => fjtg_tplHome_placesData(),
		'hotels'        => fjtg_tplHome_hotelsGridData()
	);

	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'tpl-home' );
	rojak_write_to_file( $file_path, json_encode( $home_all_data ) );

}

/**
 * Number of items in hotelsGrid
 */
function fjtg_tplHome_hotelsGridItems() {
	return 5;
}

/**
 * hotelsGrid: CSS Generator
 */
function fjtg_tplHome_hotelsGridCss( $custom_css_classes = array() ) {
	$css_classes   = array();
	$css_classes[] = 'grid__item';
	$css_classes[] = 'js_grid__item';
	$css_classes[] = 'grid__item--height-340';

	$css_classes = array_merge( $css_classes, $custom_css_classes );
	$css_classes = array_unique( $css_classes );

	return implode(' ', $css_classes);
}

/**
 * hotelsGrid: Data related
 */
function fjtg_tplHome_hotelsGridData() {
	$home_id     = get_option( 'page_on_front' );
	$home_hotels = CFS()->get( 'home_featured_hotels', $home_id );
	if ( ! empty( $home_hotels ) && ! rojak_empty_array( $home_hotels ) ) {

		// Get only the first 5 items in array
		$num_of_items = fjtg_tplHome_hotelsGridItems();
		$home_hotels  = array_slice( $home_hotels, 0, $num_of_items );

		$post_type = 'hotel';
		$count = 0;
		$grid_data = array();

		// get cities json for use later
		$json_cities = fjtg_json_get_posts( 'posts-city' );

		// get only the relevant hotels for homepage
		$json_hotels = fjtg_json_get_posts_hotel();
		$json_home_hotels = array();
		foreach( $json_hotels as $json_hotel ) {
			if ( in_array( $json_hotel['ID'], $home_hotels ) ) {
				$json_home_hotels[] = $json_hotel;
			}
		}

		// nested loop to retain order of hotels
		foreach( $home_hotels as $hotel_id ) {
			foreach( $json_home_hotels as $hotel ) {
				if ( $hotel_id == $hotel['ID'] ) {
					$css_classes   = array();
					$css_classes[] = 'grid__item--height-340';
					$css_classes[] = 'js_lazy-bg';

					if ( 3 == $count ) {
						$css_classes[] = 'grid__item--width-30';
					} else {
						$css_classes[] = 'grid__item--width-35';
					}
					$css_classes = fjtg_tplHome_hotelsGridCss( $css_classes );

					$grid_data[$count]['ID']            = $hotel['ID'];
					$grid_data[$count]['post_title']    = $hotel['post_title'];
					$grid_data[$count]['post_name']     = $hotel['post_name'];
					$grid_data[$count]['url']           = $hotel['hotel_url'];
					$grid_data[$count]['css']           = $css_classes;
					$grid_data[$count]['img']           = fjtg_get_img_large_thumb( 'home-grid', $hotel['ID'], $post_type );
					$grid_data[$count]['city']['title'] = $hotel['hotel_city']['post_title'];

					$grid_hotel_city_url = $json_cities[ $hotel['hotel_city']['post_name'] ]['city_url'];
					if ( ! empty( $grid_hotel_city_url ) ) {
						$grid_data[$count]['city']['url']   = $grid_hotel_city_url;
					}

					$count++;
				}
			}
		}
		return $grid_data;
	}

	return false;
}

/**
 * destination: Data related
 */
function fjtg_tplHome_placesData() {
	$home_id     = get_option( 'page_on_front' );
	$home_places = CFS()->get( 'home_featured_places', $home_id );

	if ( !empty( $home_places ) ) {
		$post_type = 'place';
		$count = 0;
		$data = array();
		foreach( $home_places as $place_id ) {
			$place_id     = (int) $place_id;
			$place_post   = get_post( $place_id );
			if ( ! rojak_empty_object( $place_post ) &&
			     'publish' == $place_post->post_status ) {
				$item_css     = array();
				$item_css[]   = "slider-hdest__item-$place_id";
				$item_css[]   = "slider-hdest__item";
				$item_css[]   = "js_slider-hdest__item";
				if ( ( $count + 1 ) % 2 == 0 ) {
					$item_css[]   = "slider-hdest__item--even";
				} else {
					$item_css[]   = "slider-hdest__item--odd";
				}

				$data[$count] = array(
					'ID'         => $place_id,
					'post_title' => $place_post->post_title,
					'post_name'  => $place_post->post_name,
					'city'       => get_post_meta( $place_id, 'place_city.post_title', true ),
					'url'        => get_permalink( $place_id ),
				);
				$data[$count]['css'] = implode( ' ', $item_css );

				$data[$count]['img'] = fjtg_get_img_large_thumb( 'home-destination', $place_id, $post_type );

				$count++;
			}
		}
		return $data;
	}

	return false;
}




function fjtg_tplHome_slideshowData() {

	$home_id = get_option( 'page_on_front' );
	$attachment_slide_tag = 'slideshow';

	$current_post_has_attachments = rojak_has_page_attachments( $home_id, $attachment_slide_tag );
	$home_has_attachments = rojak_has_page_attachments( get_option('page_on_front'), $attachment_slide_tag );

	if ( $current_post_has_attachments ) {
		$attachments = rojak_get_post_attachments( $home_id );
	} else {
		$attachments = rojak_get_post_attachments( get_option('page_on_front') );
	}

	if ( $current_post_has_attachments || $home_has_attachments ) {
		$count = 0;
		$slider_data = array();
		foreach ($attachments as $attachment) {

			// check if an attachment has the bg media tag
			$term_list = wp_get_post_terms( $attachment->ID, 'media_tag', array("fields" => "all") );
			$terms = array();
			foreach ( $term_list as $term ) {
				array_push($terms, $term->slug);
			}

			if (in_array($attachment_slide_tag, $terms)) {
				$image_info   = wp_prepare_attachment_for_js( $attachment->ID );
				$image_large  = wp_get_attachment_image_src( $image_info["id"], 'slider' );
				$image_url    = $image_large[0];
				$image_width  = $image_large[1];
				$image_height = $image_large[2];
				$image_title  = $image_info[ "title" ];

				$image_alt        = $image_info["alt"];
				if ( empty( $image_alt ) ) {
					$image_alt = $image_title;
				}

				$lang_code = str_replace( '-', '_', ICL_LANGUAGE_CODE );
				$attachment_caption = rwmb_meta( "fjtg_title_{$lang_code}", array(), $attachment->ID );
				if ( empty( $attachment_caption ) ) {
					$attachment_caption     = $image_info["caption"];
				}
				$attachment_description = rwmb_meta( "fjtg_description_{$lang_code}", array(), $attachment->ID );
				if ( empty( $attachment_description ) ) {
					$attachment_description = $image_info["description"];
				}

				// Make sure the large image size is 1600 x 800
				if ( $image_width == 1600 && $image_height == 800 ) {
					$slider_data[$count]['ID']           = $attachment->ID;
					$slider_data[$count]['post_title']   = $attachment->post_title;
					$slider_data[$count]['post_name']    = $attachment->post_name;
					$slider_data[$count]['css']          = "slider-home__item-{$attachment->ID} slider-home__item js_slider-home__item";
					$slider_data[$count]['img']['large'] = fjtg_get_img_url( 'slider', $attachment->ID );
					$slider_data[$count]['img']['thumb'] = fjtg_get_img_url( 'slider-thumb', $attachment->ID );
					if ( empty( $slider_data[$count]['img']['thumb'] ) ) {
						$slider_data[$count]['img']['thumb'] = fjtg_get_img_url( 'slider', $attachment->ID );
					}
					$slider_data[$count]['description'] = $attachment_description;
					$slider_data[$count]['caption'] = $attachment_caption;

					$count++;
				}
			}
		}
		return $slider_data;
	}
	return false;
}


function fjtg_tplHome_globalOffersData() {
	$tpl_id = rojak_get_page_id_by_tpl( 'tpl-global-offers-list' );
	if ( !empty( $tpl_id ) ) {
		$args = array(
			'post_type'        => 'page',
			'post_parent'      => $tpl_id,
			'posts_per_page'   => -1,
			'order'            => 'ASC',
			'orderby'          => 'menu_order',
			'post_status'      => 'publish',
			'suppress_filters' => 0,
		);
		$page_list = new WP_Query( $args );
		if ( $page_list->have_posts() ) {
			$count = 0;
			$offer_data = array();
			while ( $page_list->have_posts() ) { $page_list->the_post();
				$offer_data[$count] = array(
					'ID'           => $page_list->post->ID,
					'post_title'   => $page_list->post->post_title,
					'post_name'    => $page_list->post->post_name,
					'url'          => get_permalink(),
					'post_excerpt' => get_the_excerpt(),
					'img'          => fjtg_get_img_large_thumb( 'home-global-offers', $page_list->post->ID ),
				);
				$count++;
			}
			wp_reset_query();
			return $offer_data;
		}
	}
	return false;
}