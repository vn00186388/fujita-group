<?php

if ( function_exists( 'pods' ) ) {
	add_action( 'pods_meta_save_post_facility', 'fjtg_facility_pods_save_post', 10, 5 );
}

/**
 * Action when a facility post is saved
 */
function fjtg_facility_pods_save_post( $data, $pod, $post_id, $groups, $post ) {
	if ( ! empty( $post->post_name ) &&
	     ! ctype_digit( $post->post_name ) ) {
		// generate hotels inside facility_hotel
		$facility_hotel = get_post_meta( $post_id, 'facility_hotel' );
		foreach( $facility_hotel as $hotel ) {
			fjtg_json_generate_post_hotel( $hotel['ID'] );
		}

		// generate posts-facility.json
		fjtg_json_generate_posts_facility();
	}
}

/**
 * JSON generator for all the data needed in Homepage
 */
function fjtg_json_generate_posts_facility() {

	$facility_all_data  = fjtg_facility_list();
	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'posts-facility' );
	rojak_write_to_file( $file_path, json_encode( $facility_all_data ) );

}


function fjtg_facility_list() {
	$facility_data = array();

	$args = array(
		'post_type'        => 'facility',
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'suppress_filters' => 0,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
	);
	global $facility_query, $has_facility;
	$facility_query = new WP_Query($args);
	$has_facility = $facility_query->have_posts();
	if ( $has_facility ) {
		while ( $facility_query->have_posts() ) { $facility_query->the_post();
			$id  = $facility_query->post->ID;
			$key = $facility_query->post->post_name;

			// set facility post info
			$facility_data[$key] = fjtg_get_base_info( $facility_query->post );

			// set content
			if ( ! empty( $facility_query->post->post_content ) ) {
				$facility_data[$key]['post_content'] =
					do_shortcode(
						apply_filters( 'the_content',
							$facility_query->post->post_content ) );
				$facility_data[$key]['post_excerpt'] =
					rojak_get_excerpt( $facility_query->post->post_excerpt, $facility_query->post->post_content );
			}

			$meta_name  = 'facility_icon';
			$meta_value = get_post_meta( $id, $meta_name, true );
			if ( ! empty( $meta_value ) ) {
				$facility_data[$key][$meta_name] = $meta_value;
			}

			// set hotels
			$facility_hotels = get_post_meta( $id, 'facility_hotel' );
			$hotels = array();
			if($facility_hotels[0] && !empty($facility_hotels)) {
				$count_a = 0;
				foreach($facility_hotels AS $hotel) {
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
				$facility_data[$key]['hotels']  = $hotels;
			}
		}
	}
	wp_reset_query();

	if ( ! rojak_empty_array( $facility_data ) ) {
		return $facility_data;
	}

	return false;
}