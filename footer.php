<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package fjtg
 */

?>

	</div><!-- #content -->

	<footer class="footer js_footer">

		<?php get_template_part( 'template-parts/footer', 'hotel-links' ); ?>

		<?php get_template_part( 'template-parts/footer', 'nav' ); ?>

		<?php get_template_part( 'template-parts/footer', 'legal' ); ?>

	</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>