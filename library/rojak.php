<?php
/**
 * Rojak - A WordPress theme development framework.
 *
 * Rojak is a framework for developing WordPress themes.  The framework allows theme developers
 * to quickly build themes without having to handle all of the "logic" behind the theme or having to
 * code complex functionality for features that are often needed in themes.  The framework does these
 * things for developers to allow them to get back to what matters the most:  developing and designing
 * themes. Themes handle all the markup, style, and scripts while the framework handles the logic.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   Rojak
 * @version   0.9.0
 * @author    Fastbooking <studioweb-fb@fastbooking.net>
 * @copyright Copyright (c) 2016, Fastbooking
 * @link      http://
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! class_exists( 'Rojak' ) ) {

	/**
	 * The Rojak class launches the framework.  It's the organizational structure behind the
	 * entire framework.  This class should be loaded and initialized before anything else within
	 * the theme is called to properly use the framework.
	 *
	 * After parent themes call the Rojak class, they should perform a theme setup function on
	 * the `after_setup_theme` hook with a priority no later than 11.  This allows the class to
	 * load theme-supported features at the appropriate time, which is on the `after_setup_theme`
	 * hook with a priority of 12.
	 *
	 * Note that while it is possible to extend this class, it's not usually recommended unless
	 * you absolutely know what you're doing and expect your sub-class to break on updates.  This
	 * class often gets modifications between versions.
	 *
	 * @since  0.9.0
	 * @access public
	 */
	class Rojak {

		/**
		 * Constructor method for the Rojak class.  This method adds other methods of the
		 * class to specific hooks within WordPress.  It controls the load order of the
		 * required files for running the framework.
		 *
		 * @since  0.9.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Set up an empty object to work with.
			$GLOBALS['rojak'] = new stdClass;

			// Set up the load order.
			add_action( 'after_setup_theme', array( $this, 'constants'     ), -95 );
			add_action( 'after_setup_theme', array( $this, 'core'          ), -95 );
			add_action( 'after_setup_theme', array( $this, 'theme_support' ),  12 );

			// add_action( 'after_setup_theme', array( $this, 'includes'      ),  13 );
			// add_action( 'after_setup_theme', array( $this, 'extensions'    ),  14 );
			// add_action( 'after_setup_theme', array( $this, 'admin'         ),  95 );
		}

		/**
		 * Defines the constant paths for use within the core framework, parent theme, and
		 * child theme.
		 *
		 * @since  0.9.0
		 * @access public
		 * @return void
		 */
		public function constants() {

			// Sets the framework version number.
			define( 'ROJAK_VERSION', '0.9.0' );

			// Theme directory paths.
			define( 'ROJAK_PARENT', trailingslashit( get_template_directory()   ) );
			define( 'ROJAK_CHILD',  trailingslashit( get_stylesheet_directory() ) );

			// Theme directory URIs.
			define( 'ROJAK_PARENT_URI', trailingslashit( get_template_directory_uri()   ) );
			define( 'ROJAK_CHILD_URI',  trailingslashit( get_stylesheet_directory_uri() ) );

			// Sets the path to the core framework directory.
			if ( ! defined( 'ROJAK_DIR' ) )
				define( 'ROJAK_DIR', trailingslashit( ROJAK_PARENT . basename( dirname( __FILE__ ) ) ) );

			// Sets the path to the core framework directory URI.
			if ( ! defined( 'ROJAK_URI' ) )
				define( 'ROJAK_URI', trailingslashit( ROJAK_PARENT_URI . basename( dirname( __FILE__ ) ) ) );

			// Core framework directory paths.
			// define( 'ROJAK_ADMIN',     trailingslashit( ROJAK_DIR . 'admin'     ) );
			define( 'ROJAK_INC',       trailingslashit( ROJAK_DIR . 'inc'       ) );
			// define( 'ROJAK_EXT',       trailingslashit( ROJAK_DIR . 'ext'       ) );
			// define( 'ROJAK_CUSTOMIZE', trailingslashit( ROJAK_DIR . 'customize' ) );

			// Core framework directory URIs.
			// define( 'ROJAK_CSS', trailingslashit( ROJAK_URI . 'css' ) );
			// define( 'ROJAK_JS',  trailingslashit( ROJAK_URI . 'js'  ) );
		}

		/**
		 * Loads the core framework files.
		 *
		 * @since  0.9.0
		 * @access public
		 * @return void
		 */
		public function core() {


			// Load the functions files.
			require_once( ROJAK_INC . 'functions-content.php'      );
			require_once( ROJAK_INC . 'functions-fb-offers.php'    );
			require_once( ROJAK_INC . 'functions-file-gallery.php' );
			require_once( ROJAK_INC . 'functions-image-sizes.php'  );
			require_once( ROJAK_INC . 'functions-utility.php'      );
			require_once( ROJAK_INC . 'functions-wpml.php'         );
			require_once( ROJAK_INC . 'functions-pods.php'         );
			require_once( ROJAK_INC . 'functions-google.php'       );
			require_once( ROJAK_INC . 'functions-json.php'         );

		}

		/**
		 * Adds theme support for features that themes should be supporting.  Also, removes
		 * theme supported features from themes in the case that a user has a plugin installed
		 * that handles the functionality.
		 *
		 * @since  1.3.0
		 * @access public
		 * @return void
		 */
		public function theme_support() {

			require_if_theme_supports( 'rojak-templates', ROJAK_INC . 'functions-templates.php' );

			require_if_theme_supports( 'rojak-assets-async', ROJAK_INC . 'functions-assets-async.php' );

			require_if_theme_supports( 'rojak-assets-timestamp', ROJAK_INC . 'functions-assets-timestamp.php' );

			// Automatically add <title> to head.
			// add_theme_support( 'title-tag' );

			// Adds core WordPress HTML5 support.
			// add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

			// Remove support for the the Breadcrumb Trail extension if the plugin is installed.
			// if ( function_exists( 'breadcrumb_trail' ) || class_exists( 'Breadcrumb_Trail' ) )
			// 	remove_theme_support( 'breadcrumb-trail' );

			// Remove support for the the Cleaner Gallery extension if the plugin is installed.
			// if ( function_exists( 'cleaner_gallery' ) || class_exists( 'Cleaner_Gallery' ) )
			// 	remove_theme_support( 'cleaner-gallery' );

			// Remove support for the the Get the Image extension if the plugin is installed.
			// if ( function_exists( 'get_the_image' ) || class_exists( 'Get_The_Image' ) )
			// 	remove_theme_support( 'get-the-image' );
		}

	}
}
