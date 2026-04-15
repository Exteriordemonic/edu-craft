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

	// Placeholder for Task 07.
}
