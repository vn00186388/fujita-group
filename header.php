<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package fjtg
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<script>var $fjtg_url = "<?php echo trailingslashit( get_bloginfo( 'wpurl' ) ); ?>";</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page" class="hfeed site js_site">

	<header class="header js_header">
		<div class="header--layout-center o_layout-center">

			<?php get_template_part( 'template-parts/header', 'utility' ) ?>

			<?php get_template_part( 'template-parts/header', 'nav' ) ?>

			<?php get_template_part( 'template-parts/header', 'logo' ) ?>

		</div>
	</header>

	<div id="content" class="site-content">