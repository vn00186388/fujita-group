<?php

if ( function_exists( 'pods' ) ) {
	add_action( 'pods_meta_save_post_city', 'fjtg_city_pods_save_post', 10, 5 );
}

/**
 * Action when a city post is saved
 */
function fjtg_city_pods_save_post( $data, $pod, $post_id, $groups, $post ) {
	fjtg_json_generate_posts_city();
}

/**
 * JSON generator for all the data needed in Homepage
 */
function fjtg_json_generate_posts_city() {

	$city_all_data  = fjtg_city_list();
	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'posts-city' );
	rojak_write_to_file( $file_path, json_encode( $city_all_data ) );

}


function fjtg_city_list() {
	$city_data = array();

	$args = array(
		'post_type'        => 'city',
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'suppress_filters' => 0,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
	);
	global $city_query, $has_city;
	$city_query = new WP_Query($args);
	$has_city = $city_query->have_posts();
	if ( $has_city ) {
		while ( $city_query->have_posts() ) { $city_query->the_post();
			$id  = $city_query->post->ID;
			$key = $city_query->post->post_name;

			$city_data[$key] = fjtg_get_base_info( $city_query->post );

			// $meta_object = rojak_get_post_meta_object( $id, 'city_country' );
			// $city_data[$key]['hotel_country'] = fjtg_get_base_info( $meta_object );
			// $city_data[$key]['hotel_country']['country_url'] =
			// 	chendol_get_country_city_permalink(
			// 		$city_data[$key]['hotel_country']['post_name']
			// 	);

			// // set city_url
			// $city_url_path = $city_data[$key]['hotel_country']['post_name'] . '/' . $key;
			// $city_url      = chendol_get_country_city_permalink( $city_url_path );
			// if ( ! empty( $city_url ) ) {
			// 	$city_data[$key]['city_url'] = $city_url;
			// }

			$city_hotels = get_post_meta( $id, 'city_hotel' );
			$hotels = array();
			if($city_hotels[0] && !empty($city_hotels)) {
				$count_a = 0;
				foreach($city_hotels AS $hotel) {
					$hotel = fjtg_json_get_post_hotel( $hotel['post_name'] );
					$hotel = (object) $hotel;
					$hotels[$count_a] = fjtg_get_base_info( $hotel );

					if ( ! empty( $hotel->featured_img ) ) {
						$hotels[$count_a]['featured_img'] = $hotel->featured_img;
					}
					$hotels[$count_a]['hotel_url'] = $hotel->hotel_url;

					$count_a++;
				}
			}
			if ( ! rojak_empty_array( $hotels ) ) {
				$city_data[$key]['hotels']  = $hotels;
			}
		}
	}
	wp_reset_query();

	if ( ! rojak_empty_array( $city_data ) ) {
		return $city_data;
	}

	return false;
}