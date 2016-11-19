<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package fjtg
 */

get_header(); ?>

<div class="hero hero_404">
	<div class="hero_404__content">
		<h1 class="hero_404__heading">404</h1>
		<h4 class="hero_404__texts"><?php _e( 'Oops.. It seems the page you are looking for does not exist. Please go back to our <a href="/">homepage.</a>', 'fjtg' ) ?></h4>
	</div>
</div>


<?php get_footer();