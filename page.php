<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package fjtg
 */

get_header(); ?>

<?php get_template_part( 'template-parts/hero', 'page' ); ?>

<?php get_template_part( 'template-parts/breadcrumb' ); ?>

<?php get_template_part( 'template-parts/content', 'page' ); ?>

<?php get_footer();