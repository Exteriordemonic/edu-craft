<?php
/**
 * Plugin Name: Edu Craft Domain
 * Description: Domain logic module for the EduCraft project.
 * Version: 0.1.0
 * Author: EduCraft
 * Text Domain: edu-craft-domain
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDU_CRAFT_DOMAIN_VERSION', '0.1.0' );
define( 'EDU_CRAFT_DOMAIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EDU_CRAFT_DOMAIN_URL', plugin_dir_url( __FILE__ ) );

require_once EDU_CRAFT_DOMAIN_PATH . 'includes/helpers/dependencies.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/acf/register.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/post-types/register.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/taxonomies/register.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/woocommerce/hooks.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/rest/routes.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/admin/hooks.php';
require_once EDU_CRAFT_DOMAIN_PATH . 'includes/cli/commands.php';

/**
 * Boots plugin modules after dependency checks.
 *
 * @return void
 */
function edu_craft_domain_boot() {
	$missing_dependencies = edu_craft_domain_get_missing_dependencies();

	if ( ! empty( $missing_dependencies ) ) {
		edu_craft_domain_boot_dependency_error_handlers( $missing_dependencies );
		return;
	}

	edu_craft_domain_register_post_types();
	edu_craft_domain_register_taxonomies();
	edu_craft_domain_register_acf_hooks();
	edu_craft_domain_register_woocommerce_hooks();
	edu_craft_domain_register_rest_routes();
	edu_craft_domain_register_admin_hooks();
	edu_craft_domain_register_cli_commands();
}
add_action( 'plugins_loaded', 'edu_craft_domain_boot' );
