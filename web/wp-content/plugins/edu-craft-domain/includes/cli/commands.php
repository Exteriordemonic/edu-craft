<?php
/**
 * CLI helpers module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers CLI commands when WP-CLI is available.
 *
 * @return void
 */
function edu_craft_domain_register_cli_commands() {
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
		return;
	}

	require_once EDU_CRAFT_DOMAIN_PATH . 'includes/cli/seed-demo.php';

	add_action( 'wp_loaded', 'edu_craft_domain_cli_register_wp_cli_commands', 20 );
}

/**
 * Registers WP-CLI commands after CPT/taxonomies and WooCommerce are ready.
 *
 * @return void
 */
function edu_craft_domain_cli_register_wp_cli_commands() {
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
		return;
	}

	WP_CLI::add_command(
		'edu-craft seed-demo',
		new Edu_Craft_Domain_CLI_Seed_Demo_Command()
	);
}
