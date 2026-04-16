<?php
/**
 * Dependency checks for the domain plugin.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a normalized list of missing runtime dependencies.
 *
 * @return string[]
 */
function edu_craft_domain_get_missing_dependencies() {
	$missing_dependencies = array();

	$acf_is_active = class_exists( 'ACF' ) || class_exists( 'acf_pro' ) || function_exists( 'acf_get_setting' );
	if ( ! $acf_is_active ) {
		$missing_dependencies[] = 'Advanced Custom Fields PRO';
	}

	$woocommerce_is_active = class_exists( 'WooCommerce' );
	if ( ! $woocommerce_is_active ) {
		$missing_dependencies[] = 'WooCommerce';
	}

	return $missing_dependencies;
}

/**
 * Handles dependency failures in admin and frontend.
 *
 * @param string[] $missing_dependencies Missing dependencies.
 * @return void
 */
function edu_craft_domain_boot_dependency_error_handlers( array $missing_dependencies ) {
	$message = sprintf(
		'The "Edu Craft Domain" plugin requires the following active plugins: %s.',
		implode( ', ', $missing_dependencies )
	);

	if ( is_admin() ) {
		add_action(
			'admin_notices',
			static function() use ( $message ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
			}
		);
		return;
	}

	wp_die(
		esc_html( $message ),
		'Missing Required Plugins',
		array( 'response' => 500 )
	);
}
