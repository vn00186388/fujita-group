<?php

if ( function_exists( 'pods' ) ) {
	add_action( 'pods_meta_save_post_hotel', 'fjtg_hotel_pods_save_post', 10, 5 );
}

/**
 * Action when a hotel post is saved
 */
function fjtg_hotel_pods_save_post( $data, $pod, $post_id, $groups, $post ) {
	// block save-as-draft, mainly a post that does not have a post_name
	// block post_name made of all digits
	if ( ! empty( $post->post_name ) &&
	     ! ctype_digit( $post->post_name ) ) {
		fjtg_json_generate_post_hotel( $post_id, $post );
	}
}

/**
 * JSON generator for each hotel
 */
function fjtg_json_generate_post_hotel( $post_id, $post = null ) {

	$hotel = $post;
	if ( empty( $hotel ) ) {
		$hotel = get_post( $post_id );
	}

	$hotel_data = fjtg_hotel_post( $hotel );
	$hotel_data['hotel_logo'] = pods_image_url ( $hotel_data['hotel_logo'], $size = null, $default = 0, $force = false );
	$hotel_data['hotel_logo_mobile'] = pods_image_url ( $hotel_data['hotel_logo_mobile'], $size = null, $default = 0, $force = false );
	$hotel_data['hotel_faq_feature_image'] = pods_image_url ( $hotel_data['hotel_faq_feature_image'], $size = null, $default = 0, $force = false );
	$hotel_fields = pods('hotel', $hotel_data['ID']);
	$hotel_data['facility_hotel'] = $hotel_fields->field('facility_hotel');
	$file_path  = rojak_get_json_site_dir( 'per-hotel' );
	$file_path .= fjtg_hotel_get_json_filename( $hotel->post_name );
	rojak_write_to_file( $file_path, json_encode( $hotel_data, JSON_PRETTY_PRINT ) );

	fjtg_json_generate_hotel_list();
	fjtg_json_generate_hotel_list_order();
	fjtg_json_generate_hotel_list_minimal();
}

function fjtg_hotel_get_json_filename( $post_name ) {
	$filename_ext = '-' . ICL_LANGUAGE_CODE . '.json';
	$filename = $post_name . $filename_ext;
	return $filename;
}

function fjtg_hotel_post( $post ) {

	// set basic post info, returns array
	$hotel_data = fjtg_hotel_get_post_info( $post );

	// set post_status
	$hotel_data['post_status'] = $post->post_status;

	// set all post_meta
	$hotel_meta = get_post_meta( $post->ID );

	// custom post_meta which will be handled differently
	$hotel_custom_meta = array(
		'hotel_brand',
		'hotel_destination',
		'hotel_city',
		'hotel_site_id'
	);

	foreach( $hotel_custom_meta AS $meta_name ) {
		$meta_object = rojak_get_post_meta_object( $post->ID, $meta_name );

		if ( ! rojak_empty_object( $meta_object ) ) {
			if ( rojak_str_contains( $meta_name, 'site_id' ) ) {
				$hotel_data[$meta_name] = fjtg_hotel_get_site_info( $meta_object );
			} else {
				$hotel_data[$meta_name] = fjtg_hotel_get_post_info( $meta_object );
			}
		}

		// unset meta before next foreach
		unset( $hotel_meta[ $meta_name ] );
	}

	// set url
	$env_url = get_bloginfo( 'wpurl' );
	if ( rojak_str_contains( $env_url, '//fujita.dev' ) ) {
		if ( ! empty( $hotel_data['hotel_site_id'] ) ) {
			$hotel_url  = $hotel_data['hotel_site_id']['domain'];
			$hotel_url .= $hotel_data['hotel_site_id']['path'];
			$hotel_data['hotel_url'] = "//$hotel_url";
		}
	} else if ( rojak_str_contains( $env_url, '.wsdasia-sg-1.wp-ha.fastbooking.com' ) ) {
		if ( ! empty( $hotel_data['hotel_site_id']['domain'] ) ) {
			//to check ssl method
			if(is_ssl()){
				$hotel_data['hotel_url'] = 'https://';
			}else{
				$hotel_data['hotel_url'] = 'http://';
			}
			$hotel_data['hotel_url'] .= $hotel_data['hotel_site_id']['domain'];
		}
	} else {
		// lets do nothing for now
	}

	// [mon] set custom map poi, or i call it places
	$meta_name  = 'hotel_place_settings';
	$meta_value = get_post_meta( $post->ID, $meta_name );
	if ( ! rojak_empty_array( $meta_value ) ) {
		$hotel_data[$meta_name] = $meta_value;
	}
	unset( $hotel_meta[ $meta_name ] );

	// [mon] fix hotel_hotels_nearby if there is only 1
	$hotel_multi_array_meta = array(
		'hotel_hotels_nearby',
		'hotel_facility'
	);

	// default handling of other post_meta's
	foreach( $hotel_meta AS $meta => $value ) {
		$meta_value = null;
		if ( rojak_str_starts_with( $meta, 'hotel_' ) ) {
			if ( 1 == count( $value ) ) {

				// check if meta is inside the multi_array_meta
				// mainly to check if this is an array with only 1 entry
				if ( in_array( $meta, $hotel_multi_array_meta ) ) {
					$meta_value = array( fjtg_hotel_get_relationship_post( array(
						'post_id'   => $value[0],
						'meta_name' => $meta
					) ) );
				}

				// if it is not in multi_array_meta, then just pass the value
				else {
					$meta_value = $value[0];
				}

			} else if ( count( $value ) >= 2 )  {
				$count      = 0;
				$meta_value = $tmp_data;
				$tmp_data   = array();

				// loop thru the array
				foreach( $value AS $value_key => $value_post_id ) {

					$tmp_data[$count] = fjtg_hotel_get_relationship_post( array(
						'post_id'   => $value_post_id,
						'meta_name' => $meta
					) );

					$count++;
				}
				if ( ! rojak_empty_array( $tmp_data ) ) {
					$meta_value = $tmp_data;
				}
			} else {
				$meta_value = $value;
			}
			if ( ! empty( $meta_value ) || ! rojak_empty_array( $value )	) {
				$hotel_data[$meta] = $meta_value;
			}
		}
	}

	return $hotel_data;
}

/*
 * $tmp_data = fjtg_hotel_get_relationship_post( array(
 *   'post_id'   => $value_post_id,
 *   'meta_name' => $meta
 * ) );
 */
function fjtg_hotel_get_relationship_post( $args = array() ) {
	if ( ! rojak_empty_array( $args ) ) {
		$meta_post = get_post( $args['post_id'] );
		$tmp_data  = fjtg_hotel_get_post_info( $meta_post );
		if ( rojak_str_contains( $args['meta_name'], '_hotels_' ) ) {
			// $tmp_data['hotel_url']  = chendol_get_hotel_permalink( $meta_post->ID );
		}
		return $tmp_data;
	}
	return false;
}

function fjtg_hotel_get_post_info( $post ) {
	$post_data = array();
	$post_data['ID']         = (int) $post->ID;
	$post_data['post_title'] = $post->post_title;
	$post_data['post_name']  = $post->post_name;

	$featured_img = fjtg_get_featured_image_url( $post->ID, 'hotel' );
	if ( ! empty( $featured_img ) ) {
		$post_data['featured_img'] = $featured_img;
	}

	if ( ! empty( $post->post_excerpt ) ) {
		$post_data['post_excerpt'] = $post->post_excerpt;
	}

	return $post_data;
}

function fjtg_hotel_get_site_info( $site ) {
	$site_data = array();
	$site_data['blog_id'] = $site->blog_id;
	$site_data['site_id'] = $site->site_id;
	$site_data['domain']  = $site->domain;
	$site_data['path']    = $site->path;
	return $site_data;
}


function fjtg_json_get_post_hotel_filepath( $post_name ) {
	$file_path = rojak_get_json_site_dir( 'per-hotel' );
	$file_path .= fjtg_hotel_get_json_filename( $post_name );

	if ( ! is_file( $file_path ) ) {
		$args=array(
			'name'             => $post_name,
			'post_type'        => 'hotel',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'suppress_filters' => 0,
		);
		$hotel_posts = get_posts( $args );
		if( ! empty( $hotel_posts ) ) {
			fjtg_json_generate_post_hotel( $hotel_posts[0]->ID );
		}
	}

	return $file_path;
}

function fjtg_json_set_post_hotel( $post_name ) {
	$GLOBALS[$post_name] = json_decode( file_get_contents(
		fjtg_json_get_post_hotel_filepath($post_name)
	), true );
}

function fjtg_json_get_post_hotel( $post_name ) {
	if( !isset( $GLOBALS[$post_name] ) ) {
		fjtg_json_set_post_hotel($post_name);
	}

	return $GLOBALS[$post_name];
}


function fjtg_hotel_delete_json( $post_name ) {
	$file_path = rojak_get_json_site_dir( 'per-hotel' );
	$file_path .= fjtg_hotel_get_json_filename( $post_name );

	if ( is_file( $file_path ) ) {
		unlink( $file_path );
	}
}

function fjtg_json_generate_hotel_list( $return = false) {
	$directory = rojak_get_json_site_dir( 'per-hotel' );

	//get all text files with a .txt extension.
	$filename_ext = '-' . ICL_LANGUAGE_CODE . '.json';
	$files_array = glob( $directory . "*$filename_ext" );

	$hotels_data = array();
	foreach( $files_array as $file_path ) {
		$hotel = json_decode( file_get_contents( $file_path ), true );

		// skip hotel that has no post_name
		// or skip hotel that has post_name with all digits
		if ( empty( $hotel['post_name'] ) ||
		     ctype_digit( $hotel['post_name'] ) ) {
			continue;
		}

		$hotels_data[ $hotel['post_name'] ] = $hotel;
	}

	// add the offers on their respective hotel data
	foreach( $hotels_data AS $post_name => $hotel ) {
		$current_hotel = $hotel;
		$hotels_data[ $post_name ] = $current_hotel;
	}

	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'posts-hotel' );
	rojak_write_to_file( $file_path, json_encode( $hotels_data, JSON_PRETTY_PRINT ) );

	if ( $return ) {
		return $hotels_data;
	}
}

/**
 * JSON generator for Hotel list order.
 *
 * @param boolean $boolean if True then return an array of hotel list sorted.
 * @return array $hotel_list_sorted
 */
function fjtg_json_generate_hotel_list_order( $return = false) {
	$directory = rojak_get_json_site_dir( 'per-hotel' );

	//get all text files with a .txt extension.
	$filename_ext = '-' . ICL_LANGUAGE_CODE . '.json';
	$files_array = glob( $directory . "*$filename_ext" );

	$hotel_list_order = array(
		'metro-tokyo' 		=> array(),
		'hokkaido'			=> array(),
		'kinki-chugoku'		=> array(),
		'koushinetsu'		=> array(),
		'tohoku'			=> array(),
		'kyusyu-okinawa'	=> array()
	);

	foreach( $files_array as $file_path ) {
		$hotel = json_decode( file_get_contents( $file_path ), true );

		// skip hotel that has no post_name
		// or skip hotel that has post_name with all digits
		if ( empty( $hotel['post_name'] ) ||
			ctype_digit( $hotel['post_name'] ) ) {
			continue;
		}
		//prevent repeat hotel area as the array key
		if( !array_key_exists( $hotel[ 'hotel_area' ], $hotel_list_order ) ){
			array_push($hotel_list_order, $hotel['hotel_area']);
		}
		$hotel_list_order[$hotel['hotel_area']][$hotel['hotel_order']] = array(
			'post_name'		=> $hotel['post_name'],
			'post_title'	=> $hotel['post_title'],
			'href'			=> $hotel['hotel_url'],
		);
	}

	//Sort hotels with their respective areas.
	$hotel_list_sorted = array();
	foreach ($hotel_list_order as $area => $hotel_list){
		if ( ksort($hotel_list, SORT_NUMERIC ) ){
			switch ($area){
				case 'metro-tokyo':
					$area = 'Metropolitan Area/Tokyo';
					break;
				case 'hokkaido':
					$area = 'Hokkaido';
					break;
				case 'kinki-chugoku':
					$area = 'Kinki-Chugoku';
					break;
				case 'koushinetsu':
					$area = 'Koushinetsu';
					break;
				case 'tohoku':
					$area = 'Tohoku';
					break;
				case 'kyusyu-okinawa':
					$area = 'Kyusyu/Okinawa';
					break;
			}
			$hotel_list_sorted[$area] = $hotel_list;	
		}
	}

	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'hotel-order' );
	rojak_write_to_file( $file_path, json_encode( $hotel_list_sorted, JSON_PRETTY_PRINT ) );

	if ( $return ) {
		return $hotel_list_sorted;
	}
}

/**
 * JSON generator for Hotel list in minimal format.
 *
 * @param boolean $boolean if True then return an array of hotel list data.
 * @return array $hotels_data
 */
function fjtg_json_generate_hotel_list_minimal( $return = false) {
	$directory = rojak_get_json_site_dir( 'per-hotel' );

	//get all text files with a .txt extension.
	$filename_ext = '-' . ICL_LANGUAGE_CODE . '.json';
	$files_array = glob( $directory . "*$filename_ext" );

	$hotels_data = array();
	foreach( $files_array as $file_path ) {
		$hotel = json_decode( file_get_contents( $file_path ), true );

		// skip hotel that has no post_name
		// or skip hotel that has post_name with all digits
		// check hid just to make sure
		if ( empty( $hotel['post_name'] ) ||
		     ctype_digit( $hotel['post_name'] ) ||
		     empty( $hotel['hotel_fb_hid'] ) ) {
			continue;
		}

		// [mon] this json file is for public use, so
		//       hiding private-post-hotel makes sense
		if ( fjtg_hotel_is_valid( $hotel ) ) {
			$hotel_key                               = $hotel['post_name'];
			$hotels_data[$hotel_key]['hotel_url']    = $hotel['hotel_url'];
			$hotels_data[$hotel_key]['hotel_fb_hid'] = $hotel['hotel_fb_hid'];
		}
	}

	$file_path = rojak_get_json_site_dir() . fjtg_get_site_json_filenames( 'posts-hotel-minimal' );
	rojak_write_to_file( $file_path, json_encode( $hotels_data, JSON_PRETTY_PRINT ) );

	if ( $return ) {
		return $hotels_data;
	}
}
