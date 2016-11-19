<?php

if ( function_exists( 'pods' ) ) {
	add_action( 'pods_meta_save_post_brand', 'fjtg_brand_pods_save_post', 10, 5 );
}

/**
 * Action when a brand post is saved
 */
function fjtg_brand_pods_save_post( $data, $pod, $post_id, $groups, $post ) {
	fjtg_json_generate_posts_brand();
}

/**
 * JSON generator for brand data
 */
function fjtg_json_generate_posts_brand() {

	$brand_all_data  = fjtg_brand_list();
	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'posts-brand' );
	rojak_write_to_file( $file_path, json_encode( $brand_all_data ) );

}


function fjtg_brand_list() {
	$brand_data = array();

	$post_type = 'brand';
	$args = array(
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'suppress_filters' => 0,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
	);
	global $brand_query, $has_brand;
	$brand_query = new WP_Query($args);
	$has_brand = $brand_query->have_posts();
	if ( $has_brand ) {
		while ( $brand_query->have_posts() ) { $brand_query->the_post();
			$id  = $brand_query->post->ID;
			$key = $brand_query->post->post_name;

			$brand_data[$key] = fjtg_get_base_info( $brand_query->post );

			$featured_img = fjtg_get_featured_image_url( $id, $post_type );
			if ( ! empty( $featured_img ) ) {
				$brand_data[$key]['featured_img'] = $featured_img;
			}

			// set hotels
			$brand_hotels = get_post_meta( $id, 'brand_hotel' );
			$hotels = array();
			if($brand_hotels[0] && !empty($brand_hotels)) {
				$count_a = 0;
				foreach($brand_hotels AS $hotel) {
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
				$brand_data[$key]['hotels']  = $hotels;
			}

			// set all post_meta
			$brand_meta = get_post_meta( $brand_query->post->ID );
			$brand_pfx  = 'brand_';

			// remove brand_hotel since it is handled earlier
			unset( $brand_meta[$brand_pfx . 'hotel'] );

			// custom post_meta which will be handled differently
			$brand_custom_meta = array(
				$brand_pfx . 'logo_default',
				$brand_pfx . 'logo_plain_white',
				$brand_pfx . 'logo_header',
				$brand_pfx . 'logo_mobile'
			);

			foreach( $brand_custom_meta AS $meta_name ) {
				$meta = get_post_meta( $id, $meta_name, true );
				if ( ! empty( $meta ) ) {
					$brand_data[$key][$meta_name]  = $meta['guid'];
				}
				// unset meta before next foreach
				unset( $brand_meta[$meta_name] );
			}

			// default handling of other post_meta's
			foreach( $brand_meta AS $meta => $value ) {
				$meta_value = null;
				if ( rojak_str_starts_with( $meta, $brand_pfx ) ) {
					if ( count( $value ) == 1 ) {
						$meta_value = $value[0];
					} else {
						$meta_value = $value;
					}

					if ( 'brand_homepage' == $meta ) {
						$page = get_post( $value[0] );
						$page = fjtg_get_base_info( $page );
						$meta_value = $page;
					}

					if ( ! empty( $meta_value ) || ! rojak_empty_array( $value )	) {
						$brand_data[$key][$meta] = $meta_value;
					}
				}
			}
		}
	}
	wp_reset_query();

	if ( ! rojak_empty_array( $brand_data ) ) {
		return $brand_data;
	}

	return false;
}