<?php
// BreadCrumbs
function fjtg_breadcrumbs($args = array()) {

	global $FB_CHENDOL;

	$defaults = array(
		'delimiter'      => '<span class="delimiter">/</span>',	// delimiter between two levels
		'before'         => '<div class="breadcrumbs o_layout-center">',	// code before the breadcrumb section
		'after'          => '</div>',	// code after the breadcrumb section
		'before_current' => '<span class="current">',	// code before the current crumb
		'after_current'  => '</span>',	// code after the current crumb
		'show_home'      => true,	// show/hide the home link
		'home'           => __('Holiday Villa', 'fjtg'), // text for the 'Home' link
		'echo'           => true,	// echo the output or return it
	);

	$args = wp_parse_args( $args, $defaults );

	if ( !is_home() && !is_front_page() || is_paged() ) {

		global $post;
		global $wp_query;

		$strBreadCrumbs = $args['before'];
		$homeLink = get_bloginfo('url');

		if ( $args['show_home'] ) {
			$strBreadCrumbs .= '<a href="' . $homeLink . '">' . $args['home'] . '</a> ' . $args['delimiter'] . ' ';
		}

		if ( is_category() ) {
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0) $strBreadCrumbs .=(get_category_parents($parentCat, TRUE, ' ' . $args['delimiter'] . ' '));
			$strBreadCrumbs .= $args['before_current'] . single_cat_title('', false) . $args['after_current'];

		} elseif ( is_day() ) {
			$strBreadCrumbs .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $args['delimiter'] . ' ';
			$strBreadCrumbs .= '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $args['delimiter'] . ' ';
			$strBreadCrumbs .= $args['before_current'] . get_the_time('d') . $args['after_current'];

		} elseif ( is_month() ) {
			$strBreadCrumbs .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $args['delimiter'] . ' ';
			$strBreadCrumbs .= $args['before_current'] . get_the_time('F') . $args['after_current'];

		} elseif ( is_year() ) {
			$strBreadCrumbs .= $args['before_current'] . get_the_time('Y') . $args['after_current'];

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				$strBreadCrumbs .= '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $args['delimiter'] . ' ';
				$strBreadCrumbs .= $args['before_current'] . get_the_title() . $args['after_current'];
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				$strBreadCrumbs .= get_category_parents($cat, TRUE, ' ' . $args['delimiter'] . ' ');
				$strBreadCrumbs .= $args['before_current'] . get_the_title() . $args['after_current'];
			}

		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			$strBreadCrumbs .= $args['before_current'] . $post_type->labels->singular_name . $args['after_current'];

		} elseif ( is_attachment() ) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			$strBreadCrumbs .= get_category_parents($cat, TRUE, ' ' . $args['delimiter'] . ' ');
			$strBreadCrumbs .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $args['delimiter'] . ' ';
			$strBreadCrumbs .= $args['before_current'] . get_the_title() . $args['after_current'];

		} elseif ( is_page() && !$post->post_parent ) {
			$strBreadCrumbs .= $args['before_current'] . get_the_title() . $args['after_current'];

		} elseif ( is_page() && $post->post_parent && !$FB_CHENDOL->current_hotel ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) {
				$strBreadCrumbs .= $crumb . ' ' . $args['delimiter'] . ' ';
			}
			$strBreadCrumbs .= $args['before_current'] . get_the_title() . $args['after_current'];

		} elseif ( is_page() && $post->post_parent && $FB_CHENDOL->current_hotel ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			}
			if($FB_CHENDOL->current_page_slug) {
				$current_hotel = $FB_CHENDOL->current_hotel;
				$current_blog_id = $FB_CHENDOL->get_current_blog_id();
				$page_data = $FB_CHENDOL->blog_switch_get_page( $current_blog_id );
				$breadcrumbs[] = '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
				$hotel_url = $FB_CHENDOL->hotel_list[$current_hotel]['hotel_url'];
				$hotel_title = $FB_CHENDOL->hotel_list[$current_hotel]['post_title'];
				if($hotel_url) {
					$breadcrumbs[] = '<a href="' . $hotel_url . '">' . $hotel_title . '</a>';
				}
				else {
					$breadcrumbs[] = $args['before_current'] . $hotel_title . $args['after_current'];
				}
				$span = $args['before_current'] . $page_data->post_title . $args['after_current'];
			}
			else {
				$current_hotel = $FB_CHENDOL->current_hotel;
				$breadcrumbs[] = '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
				$span = $args['before_current'] . $FB_CHENDOL->hotel_list[$current_hotel]['post_title'] . $args['after_current'];
			}
			//$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) {
				$strBreadCrumbs .= $crumb . ' ' . $args['delimiter'] . ' ';
			}
			$strBreadCrumbs .= $span;

		} elseif ( is_search() ) {
			$strBreadCrumbs .= $args['before_current'] . 'Search results for "' . get_search_query() . '"' . $args['after_current'];

		} elseif ( is_tag() ) {
			$strBreadCrumbs .= $args['before_current'] . 'Posts tagged "' . single_tag_title('', false) . '"' . $args['after_current'];

		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata($author);
			$strBreadCrumbs .= $args['before_current'] . 'Articles posted by ' . $userdata->display_name . $args['after_current'];

		} elseif ( is_404() ) {
			$strBreadCrumbs .= $args['before_current'] . '404 Not Found' . $args['after_current'];
		}

		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $strBreadCrumbs .=' (';
			$strBreadCrumbs .= ' - ' . __('Page') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $strBreadCrumbs .= ')';
		}

		$strBreadCrumbs .= $args['after'];
		return  $strBreadCrumbs;
	}
}
add_shortcode( 'breadcrumbs', 'fjtg_breadcrumbs' );
?>