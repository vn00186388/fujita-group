<?php


if ( ! function_exists( 'fjtg_get_img_url' ) ) :
function fjtg_get_img_url( $size = null, $id = null ){

	if ( $size && $id ) {

		$detect = new Mobile_Detect;

		if ( rojak_str_contains( $size, '-thumb' ) ) {

			if( $detect->isMobile() && !$detect->isTablet() ) {

				if ( rojak_str_contains( $size, 'home-grid' ) ) {
					$size = 'home-grid-mobile-thumb';
				}
				// else if ( rojak_str_contains( $size, 'slider' ) ) {
				// 	$size = 'slider-mobile-thumb';
				// } else if ( rojak_str_contains( $size, 'gallery' ) ) {
				// 	$size = 'gallery-x-mobile-thumb';
				// }

			}

		} else {

			if( $detect->isMobile() && !$detect->isTablet() ) {

				// Check $size should contain 'slider' media tag only
				if ( rojak_str_contains( $size, 'slider' ) &&
						 !rojak_str_contains( $size, '-slider' ) ) {
					$size = 'slider-mobile';
				} else if ( rojak_str_contains( $size, 'home-grid' ) ) {
					$size = 'home-grid-mobile';
				}

			} else if( $detect->isTablet() ) {

				// Check $size should contain 'slider' media tag only
				if (  rojak_str_contains( $size, 'slider' ) &&
						 !rojak_str_contains( $size, '-slider' ) ) {
					$size = 'slider-tablet';
				}

			}

		}

		$url = wp_get_attachment_image_src( $id, $size );
		if ( ! rojak_empty_array( $url ) ) {
			return $url[0];
		}

	}

}
endif;


if ( ! function_exists( 'fjtg_get_placeholder_site_logo_by_size' ) ) {
	function fjtg_get_placeholder_site_logo_by_size( $size ) {

		if ( !empty( $size ) ) {
			$image_size = rojak_get_image_size( $size );
			if ( ! rojak_empty_array( $image_size ) ) {
				$temp_img['w'] = $image_size['width'];
				$temp_img['h'] = $image_size['height'];
			}
		}
		$temp_img['txt'] = null;
		$temp_img['bg']  = 'eeeeee';

		$output .= '<span class="placeholder-img__site-logo" >';
		$output .= 	'<img class="placeholder-img__site-logo__img" ';
		$output .= 		'src="' . $GLOBALS['logo']  . '"';
		$output .= 	' />';
		$output .= 	'<img class="placeholder-img__site-logo__temp" ';
		$output .= 		'src="' . rojak_get_placeholder_image( $temp_img )  . '"';
		$output .= 	' />';
		$output .= '</span>';

		return $output;
	}
}

if ( ! function_exists('fjtg_get_logo_url') ) {
	/**
	 * Get logo URL inspired by Alchemy mobile
	 */
	function fjtg_get_logo_url() {

		$child_logo_path  = ROJAK_CHILD      . "img/logo.svg";
		$child_logo_url   = ROJAK_CHILD_URI  . "img/logo.svg";
		$parent_logo_path = ROJAK_PARENT     . "img/logo.svg";
		$parent_logo_url  = ROJAK_PARENT_URI . "img/logo.svg";

		// Check if /img/logo.svg exist in Child Theme
		if ( file_exists( $child_logo_path ) ) {
			return apply_filters( 'fjtg_logo_url', $child_logo_url );
		}
		elseif ( file_exists( $parent_logo_path ) ) {
			return apply_filters( 'fjtg_logo_url', $parent_logo_url );
		}
		// If does not exist then check in group site hotel data
		else {
			$logo_placeholder = array(
					'txtsize' => 20,
					'w'       => 400,
					'h'       => 40,
					'txtclr'  => '000000'
				);
			return apply_filters( 'fjtg_logo_url', rojak_get_placeholder_image( $logo_placeholder ) );
		}
		return apply_filters('fjtg_logo_url', false);
	}
}

if ( ! function_exists('fjtg_get_logo_header_utility_url') ) {
	function fjtg_get_logo_header_utility_url() {

		$child_logo_path  = ROJAK_CHILD      . "img/logo-header-utility.svg";
		$child_logo_url   = ROJAK_CHILD_URI  . "img/logo-header-utility.svg";
		$parent_logo_path = ROJAK_PARENT     . "img/logo-header-utility.svg";
		$parent_logo_url  = ROJAK_PARENT_URI . "img/logo-header-utility.svg";

		if ( file_exists( $child_logo_path ) ) {
			return $child_logo_url;
		}
		elseif ( file_exists( $parent_logo_path ) ) {
			return $parent_logo_url;
		}
		return false;
	}
}


if ( ! function_exists('fjtg_get_post_thumbnail_url') ) {
	function fjtg_get_post_thumbnail_url( $post_id, $post_type = 'page' ) {

		$image_info = rojak_get_featured_image( $post_id, $post_type );
		$item_image_url = fjtg_get_img_url(
			'global-offers',
			$image_info["id"]
		);

		return $item_image_url;
	}
}


if ( ! function_exists( 'fjtg_get_post_first_slideshow_img' ) ) {
	function fjtg_get_post_first_slideshow_img( $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_option( 'page_on_front' );
		}

		$hero_img = fjtg_get_post_first_feature_img( $post_id );

		if ( !$hero_img ) {
			$hero_img = fjtg_get_post_first_feature_img( get_option( 'page_on_front' ) );
		}

		return $hero_img;
	}
}

if ( ! function_exists( 'fjtg_get_post_first_feature_img' ) ) {
	function fjtg_get_post_first_feature_img( $post_id ) {
		$argsThumb = array(
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_type'      => 'attachment',
			'post_parent'    => rojak_get_primary_lang_post_id( $post_id ),
			'post_mime_type' => 'image',
			'post_status'    => null
		);

		$attachments = get_posts( $argsThumb );
		if ( $attachments ) {
			$first = true;
			foreach ( $attachments as $attachment ) {
				if ( $first ) {
					$first = false;
					return wp_get_attachment_url($attachment->ID);
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'fjtg_is_hotel_page' ) ) {
	function fjtg_is_hotel_page( ) {

		global $FB_CHENDOL;
		$hotel = $FB_CHENDOL->current_hotel;

		if ( $hotel ) {
			return $hotel;
		}

		return false;
	}
}


if ( ! function_exists( 'fjtg_is_brand_valid' ) ) {
	function fjtg_is_brand_valid( ) {
		$brand = fjtg_get_hotel_brand();
		if ( empty( $brand['brand_logo_header'] ) ||
			 empty( $brand['brand_color_scheme'] ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'fjtg_get_current_page_template' ) ) {
	function fjtg_get_current_page_template( ) {

		if( !isset( $GLOBALS['fjtg_current_page_template'] ) ) {
			global $post;
			$GLOBALS['fjtg_current_page_template'] = get_post_meta( $post->ID, '_wp_page_template', true );
		}

		return $GLOBALS['fjtg_current_page_template'];
	}
}



if ( ! function_exists( 'fjtg_get_hotel_id' ) ) {
	function fjtg_get_hotel_id( ) {

		global $FB_CHENDOL;
		if ( $FB_CHENDOL->current_hotel ) {
			$hotel_post = $FB_CHENDOL->get_current_hotel_post();
			return $hotel_post->ID;
		}

		return false;

	}
}


if ( ! function_exists( 'fjtg_get_hotel_brand' ) ) {
	function fjtg_get_hotel_brand() {

		if( !isset( $GLOBALS['fjtg_hotel_brand'] ) ) {
			global $FB_CHENDOL;
			$hotel  = fjtg_json_get_post_hotel( $FB_CHENDOL->current_hotel );
			$hotel_brand = $hotel['hotel_brand']['post_name'];
			$brands = fjtg_json_get_posts( 'posts-brand' );
			if ( ! empty( $hotel_brand ) &&
				 ! empty( $brands[ $hotel_brand ] ) ) {
				$GLOBALS['fjtg_hotel_brand'] = $brands[ $hotel_brand ];
			}

			// Detect if hotel is private
			if ( 'private' == $hotel['post_status'] &&
				  ! is_user_logged_in() ) {
				$GLOBALS['fjtg_hotel_brand'] = null;
			}
		}

		return $GLOBALS['fjtg_hotel_brand'];

	}
}

if ( ! function_exists( 'fjtg_get_current_brand' ) ) {
	function fjtg_get_current_brand( ) {
		if( !isset( $GLOBALS['fjtg_get_current_brand'] ) ) {
			if ( 'brand' == get_post_type() ) {
				global $post;
				$current_brand = fjtg_json_get_posts( 'posts-brand' );
				$current_brand = $current_brand[ $post->post_name ];
				$GLOBALS['fjtg_get_current_brand'] = $current_brand;
			} else if ( fjtg_is_hotel_page() ) {
				$GLOBALS['fjtg_get_current_brand'] = fjtg_get_hotel_brand();
			} else {
				$GLOBALS['fjtg_get_current_brand'] = null;
			}
		}

		return $GLOBALS['fjtg_get_current_brand'];
	}
}


/*
* Google Map static URL
*/
if ( ! function_exists( 'fjtg_google_map_static_url' ) ) {
	function fjtg_google_map_static_url( $address, $latlng, $zoom = 16 ) {
		$google_map_url  = 'https://www.google.com/maps/place/';
		$google_map_url .= $address . '/';
		$google_map_url .= '@' . $latlng . ',' . $zoom . 'z';
		return $google_map_url;
	}
}

/**
 * Google Map static map url generator
 */
if ( ! function_exists( 'fjtg_google_map_static_img_url' ) ) {
	function fjtg_google_map_static_img_url( $args = array(), $hotel_latlng ) {

		$defaults = array(
			'sensor' => false,
			'size'   => '640x340',
			'scale'  => 4,
			'zoom'   => 16,
			'center' => null,
			'markers'=> 'size:mid%7Ccolor:red%7C' . $hotel_latlng,
			'key'    => 'AIzaSyDiF4d9gOX6zAdEL5FeICScbFKDHSqa1q0',
		);

		$args = wp_parse_args( $args, $defaults );
		$url  = "http://maps.googleapis.com/maps/api/staticmap";
		$url  = esc_url( $url ) . '?'  . urldecode( http_build_query( $args ) );

		return $url;

	}
}

/**
 * Google Map static image
 */
if ( ! function_exists( 'fjtg_google_map_static_img' ) ) {
	function fjtg_google_map_static_img( $hotel_latlng  ) {
		// Detect what platform
		$detect = new Mobile_Detect;
		if ( $detect->isMobile() ) {

			// If not Tablet make sure to display the correct size
			if( !$detect->isTablet() ){
				$map_args = array(
					'size'  => '340x340',
					'scale' => 2
				);
			}

			if ( $hotel_latlng ) {
				$hotel_latlng = preg_replace( '@[ 　]@u', '', $hotel_latlng );
				$google_static_img .=  '<img class="google-map__static-img" ';
				$google_static_img .= 'src="' . fjtg_google_map_static_img_url( $map_args, $hotel_latlng  ) . '" />';

				return $google_static_img;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'fjtg_jotform' ) ) {
	/**
	 * Prints JotForm based on ID
	 */
	function fjtg_jotform( $args = array() ){

		$default_args = array(
			'id'     => null,
			'name'   => null,
			'width'  => '100%',
			'height' => '200px',
			'echo'   => false,
		);
		$args = wp_parse_args( $args, $default_args );

		$jotform_var_name = "jotform_iframe_";
		if ( empty( $args['name'] ) ) {
			$jotform_var_name .= $args['id'];
		} else {
			$jotform_var_name .= $args['name'];
		}

		if (strpos( ICL_LANGUAGE_CODE, 'en' ) !== false)
			$jotform_lang = 'en-UK';
		else if (strpos( ICL_LANGUAGE_CODE, 'zh' ) !== false)
			$jotform_lang = 'zh';
		else
			$jotform_lang = ICL_LANGUAGE_CODE;

		$jotform_lang = '?language=' . $jotform_lang;

		ob_start(); ?>
	<script>
	var <?php echo $jotform_var_name ?> = {
		allowtransparency: "true",
		frameborder: "0",
		scrolling: "no",
		src: "http://form.jotform.me/form/<?php echo $args['id'] . $jotform_lang ?>",
		style: "width:<?php echo $args['width'] ?>; height:<?php echo $args['height'] ?>; border:none;",
		class: "jotform__iframe jotform__iframe--<?php echo $args['id'] ?>",
	}</script>
	<?php EOT;

		$jotform_embed = ob_get_contents();
		ob_end_clean();

		if ( $args['echo'] )
			echo $jotform_embed;
		else
			return $jotform_embed;
	}
}

if ( ! function_exists( 'fjtg_json_get_posts_filepath' ) ) {
	function fjtg_json_get_posts_filepath( $tpl ) {
		$file_path = rojak_get_json_site_dir();
		$file_path .= fjtg_get_site_json_filenames( $tpl );

		if ( ! is_file( $file_path ) ) {
			if ( 'tpl-home' == $tpl ) {
				fjtg_json_generate_tpl_home();
			} else if ( 'posts-brand' == $tpl ) {
				fjtg_json_generate_posts_brand();
			} else if ( 'posts-city' == $tpl ) {
				fjtg_json_generate_posts_city();
			} else if ( 'posts-facility' == $tpl ) {
				fjtg_json_generate_posts_facility();
			} else if ( 'posts-hotel' == $tpl ) {
				fjtg_admin_generate_per_hotel();
				fjtg_json_generate_hotel_list();
			} else if ( 'posts-hotel-minimal' == $tpl ) {
				fjtg_json_generate_hotel_list_minimal();
			} else if ( 'hotel-order' ){
				fjtg_json_generate_hotel_list_order();
			}
		}


		return $file_path;
	}
}

if ( ! function_exists( 'fjtg_json_set_posts' ) ) {
	function fjtg_json_set_posts( $fileid ) {
		$GLOBALS[$fileid] = json_decode( file_get_contents(
			fjtg_json_get_posts_filepath($fileid)
		), true );
	}
}

if ( ! function_exists( 'fjtg_json_get_posts' ) ) {
	function fjtg_json_get_posts( $fileid ) {
		if( !isset( $GLOBALS[$fileid] ) ) {
			fjtg_json_set_posts($fileid);
		}

		return $GLOBALS[$fileid];
	}
}

if ( ! function_exists( 'fjtg_get_img_large_thumb' ) ) {
	function fjtg_get_img_large_thumb( $image_size, $post_id, $post_type = 'page' ) {

		if ( !empty( $image_size ) && !empty( $post_id ) ) {
			$img = array();

			$featured_img = rojak_get_featured_image( $post_id, $post_type );

			if ( ! rojak_empty_array( $featured_img ) ) {
				$img = array(
					'large' => fjtg_get_img_url( $image_size, $featured_img["id"] ),
					'thumb' => fjtg_get_img_url( $image_size . '-thumb', $featured_img["id"] ),
				);
			}
			else {
				$placeholder_img  = rojak_get_image_size( $image_size );
				$placeholder_args = array(
					'w'       => $placeholder_img['width'],
					'h'       => $placeholder_img['height'],
					'txtsize' => 60,
					'txt'     => '',
				);
				$img = array(
					'large' => rojak_get_placeholder_image( $placeholder_args ),
					'thumb' => null,
				);
			}

			if ( empty( $img['thumb'] ) ) {
				$img['thumb'] = $img['large'];
			}

			return $img;
		}

		return false;
	}
}

if ( ! function_exists( 'fjtg_get_site_json_filenames' ) ) {
	function fjtg_get_site_json_filenames( $fileid ) {
		$filename_ext = '-' . ICL_LANGUAGE_CODE . '.json';

		$json_filenames = array(
			'tpl-home'            	=> 'tpl-home' . $filename_ext,
			'posts-destination'   	=> 'posts-destination' . $filename_ext,
			'posts-brand'        	=> 'posts-brand' . $filename_ext,
			'posts-city'          	=> 'posts-city' . $filename_ext,
			'posts-facility'      	=> 'posts-facility' . $filename_ext,
			'posts-hotel'         	=> 'posts-hotel' . $filename_ext,
			'posts-hotel-minimal'	=> 'posts-hotel-minimal' . $filename_ext,
			'hotel-order'			=> 'hotel-order' .  $filename_ext
		);

		if ( ! empty( $json_filenames[$fileid] ) ) {
			return $json_filenames[$fileid];
		}

		return false;
	}
}

if ( ! function_exists( 'fjtg_get_featured_image_url' ) ) {
	function fjtg_get_featured_image_url( $post_id, $post_type ) {
		$image_info = rojak_get_featured_image($post_id, $post_type );
		if ( $image_info['url'] ) {
			return $image_info['url'];
		}
		return false;
	}
}


if ( ! function_exists( 'fjtg_get_base_info' ) ) {
	function fjtg_get_base_info( $post ) {
		$post_data               = array();
		$post_data['ID']         = (int) $post->ID;
		$post_data['post_title'] = $post->post_title;
		$post_data['post_name']  = $post->post_name;
		$post_data['url']        = get_permalink( $post->ID );
		return $post_data;
	}
}

if ( ! function_exists( 'fjtg_get_html_placeholder' ) ) {
	function fjtg_get_html_placeholder( $args = array() ) {
		$defaults = array(
			'txt'   => null,
			'w'     => 470,
			'h'     => 250,
			'class' => null,
			'style' => null,
		);
		$args = wp_parse_args( $args, $defaults );

		$ph_args = array(
			'txt' => $args['txt'],
			'w'   => $args['w'],
			'h'   => $args['h'],
		);
		$ph_img = rojak_get_placeholder_image( $ph_args );
		$brand  = fjtg_get_hotel_brand();
		$brand_logo = $brand['brand_logo_plain_white'];

		if ( empty( $brand_logo ) ) {
			$brand_logo = ROJAK_PARENT_URI . 'img/logo-group.svg';
		}

		return <<<HTML
			<span class="placeholder-img {$args['class']}"
				style="background-image:url('$ph_img');{$args['style']}">
				<img class="placeholder-img__logo"
					src="{$brand['brand_logo_plain_white']}" />
			</span>
HTML;
	}
}

if ( ! function_exists( 'fjtg_json_get_posts_hotel' ) ) {
	function fjtg_json_get_posts_hotel() {
		$hotels = fjtg_json_get_posts( 'posts-hotel' );
		$new_hotels_data = array();
		foreach( $hotels as $hotel ) {
			if ( fjtg_hotel_is_valid( $hotel ) ) {
				$hotel_key = $hotel['post_name'];
				$new_hotels_data[ $hotel_key ] = $hotel;
			}
		}
		return $new_hotels_data;
	}
}

/**
 * Return the Hotel list in hotel-order-<language code>.json file.
 *
 * @return array
 */
if ( ! function_exists( 'fjtg_json_get_posts_hotel_order' ) ) {
	function fjtg_json_get_posts_hotel_order() {

		$hotels_order = fjtg_json_get_posts( 'hotel-order' );
		
		return $hotels_order;
	}
}

/**
 * To check a hotel is valid.
 *
 * @param array $hotel An array contains hotel's information.
 * @return boolean   
 */
if ( ! function_exists( 'fjtg_hotel_is_valid' ) ) {
	function fjtg_hotel_is_valid( $hotel = array() ) {
		// [mon] No need to check if user is logged in since this function
		//       is mainly use for REST API and QS
		if ( ! rojak_empty_array( $hotel ) &&
			 'private' != $hotel['post_status'] ) {
			return true;
		}
		return false;
	}
}