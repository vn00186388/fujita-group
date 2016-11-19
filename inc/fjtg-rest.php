<?php
// return hotel list with rates
if ( ! function_exists( 'fjtg_hotel_list_api' ) ) {
	function fjtg_hotel_list_api( $data ) {
		if( empty( $data['post_name'] ) ) {
			$hotels = fjtg_json_get_posts_hotel();
			if ( ! rojak_empty_array( $hotels ) ) {
				return $hotels;
			}
		} else {
			$hotel = fjtg_json_get_post_hotel( $data['post_name'] );
			if ( fjtg_hotel_is_valid( $hotel ) ) {
				return $hotel;
			}
		}
		return new WP_Error( 'no_data', "No data was found", array( 'status' => 404 ) );
	}
}

/**
 * Hotel list order for footer render.
 *
 * @return An array of A hotel list that get from JSON file.
 */
if ( ! function_exists( 'fjtg_hotel_list_order_api' ) ) {
	function fjtg_hotel_list_order_api() {
		$hotels_order = fjtg_json_get_posts_hotel_order();
		if ( ! rojak_empty_array( $hotels_order ) ) {
			return $hotels_order;
		}

		return new WP_Error( 'no_data', "No data was found", array( 'status' => 404 ) );
	}
}

/**
 * Return hotel list with rates
 *
 * @return array
 */
if ( ! function_exists( 'fjtg_hotel_facility_api' ) ) {
	function fjtg_hotel_facility_api( $data ) {
		if( empty( $data['post_name'] ) ) {
			// $hotels = fjtg_json_get_posts_hotel();
			$posts = fjtg_json_get_posts( 'posts-facility' );
			if ( ! rojak_empty_array( $posts ) ) {
				return $posts;
			}
		}
		else {
			$hotel = fjtg_json_get_post_hotel( $data['post_name'] );
			if ( fjtg_hotel_is_valid( $hotel ) &&
			     ! rojak_empty_array( $hotel['hotel_facility'] ) ) {
				$facilities = fjtg_json_get_posts( 'posts-facility' );
				$hotel_facilities = array();
				foreach( $hotel['hotel_facility'] as $facility ) {
					$hotel_facility = $facilities[ $facility['post_name'] ];
					unset( $hotel_facility['hotels'] );
					$hotel_facilities[] = $hotel_facility;
				}
				return $hotel_facilities;
			}
		}
		return new WP_Error( 'no_data', "No data was found", array( 'status' => 404 ) );
	}
}

// return hotel brand
if(!function_exists('fjtg_api_post_brands')) {
	function fjtg_api_post_brands( $url_param ) {
		$posts = fjtg_json_get_posts( 'posts-brand' );

		if( empty( $url_param['post_name'] ) ) {
			if ( ! rojak_empty_array( $posts ) ) {
				return $posts;
			}
		} else {
			if ( ! rojak_empty_array( $posts[ $url_param['post_name'] ] ) ) {
				return $posts[ $url_param['post_name'] ];
			}
		}

		return new WP_Error( 'no_data', "No data was found", array( 'status' => 404 ) );
	}
}

if ( ! function_exists( 'fjtg_api_post_cities' ) ) {
	function fjtg_api_post_cities( $url_param ) {
		$posts = fjtg_json_get_posts( 'posts-city' );

		if( empty( $url_param['post_name'] ) ) {
			if ( ! rojak_empty_array( $posts ) ) {
				return $posts;
			}
		} else {
			if ( ! rojak_empty_array( $posts[ $url_param['post_name'] ] ) ) {
				return $posts[ $url_param['post_name'] ];
			}
		}

		return new WP_Error( 'no_data', "No data was found", array( 'status' => 404 ) );
	}
}

add_action('rest_api_init', function () {
	// hotel list with rates, show all
	register_rest_route('fujita-group/v1', '/hotels/', array(
		'methods' => 'GET',
		'callback' => 'fjtg_hotel_list_api',
	));

	// hotel list ordered, show all
	register_rest_route('fujita-group/v1', '/hotels/order', array(
		'methods' => 'GET',
		'callback' => 'fjtg_hotel_list_order_api',
	));
	
	// hotel list with rates, show only one hotel with the post_name
	register_rest_route('fujita-group/v1', '/hotels/(?P<post_name>.*)', array(
		'methods' => 'GET',
		'callback' => 'fjtg_hotel_list_api',
	));

	// list of all hotel facilities
	register_rest_route('fujita-group/v1', '/hotel-facilities/', array(
		'methods' => 'GET',
		'callback' => 'fjtg_hotel_facility_api',
	));

	// list of all facilities in a hotel
	register_rest_route('fujita-group/v1', '/hotel-facilities/(?P<post_name>.*)', array(
		'methods' => 'GET',
		'callback' => 'fjtg_hotel_facility_api',
	));

	// return brands
	register_rest_route('fujita-group/v1', '/brands/', array(
		'methods' => 'GET',
		'callback' => 'fjtg_api_post_brands',
	));
	register_rest_route('fujita-group/v1', '/brands/(?P<post_name>.*)', array(
		'methods' => 'GET',
		'callback' => 'fjtg_api_post_brands',
	));

	// return cities
	register_rest_route('fujita-group/v1', '/cities/', array(
		'methods' => 'GET',
		'callback' => 'fjtg_api_post_cities',
	));
	register_rest_route('fujita-group/v1', '/cities/(?P<post_name>.*)', array(
		'methods' => 'GET',
		'callback' => 'fjtg_api_post_cities',
	));

});