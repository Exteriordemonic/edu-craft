<?php
/**
 * Theme bootstrap file.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDU_CRAFT_THEME_VERSION', '1.0.0' );
define( 'EDU_CRAFT_THEME_PATH', trailingslashit( get_template_directory() ) );
define( 'EDU_CRAFT_THEME_URI', trailingslashit( get_template_directory_uri() ) );

// Check if ACF Pro is active. If not, show an admin error and prevent theme execution.
add_action( 'after_setup_theme', function() {
	if ( ! class_exists( 'acf_pro' ) ) {
		// Show admin notice
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p>';
			esc_html_e( 'The "Edu Craft Theme" requires the Advanced Custom Fields PRO plugin to function properly. Please install and activate ACF PRO.', 'edu-craft-theme' );
			echo '</p></div>';
		} );

		// Show notice on the front end as well
		if ( ! is_admin() ) {
			$error = new WP_Error(
				'edu_craft_acf_missing',
				__( 'The "Edu Craft Theme" requires the Advanced Custom Fields PRO plugin to function properly. Please install and activate ACF PRO.', 'edu-craft-theme' )
			);
			echo esc_html( $error->get_error_message() );
			die;
		}
	}
} );



$edu_craft_theme_includes = array(
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/block-helpers.php',
	'inc/blocks.php',
	'inc/acf.php',
	'inc/woocommerce.php',
	'inc/hooks.php',
);

foreach ( $edu_craft_theme_includes as $edu_craft_include_file ) {
	$edu_craft_include_path = EDU_CRAFT_THEME_PATH . $edu_craft_include_file;

	if ( file_exists( $edu_craft_include_path ) ) {
		require_once $edu_craft_include_path;
	}
}
