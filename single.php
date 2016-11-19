<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package fjtg
 */

get_header(); ?>

<?php get_template_part( 'template-parts/hero', 'page' ); ?>

<?php get_template_part( 'template-parts/breadcrumb' ); ?>

<div class="o_layout-center">
	<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', get_post_format() );

		endwhile; // End of the loop.
	?>
</div>

<?php get_footer();