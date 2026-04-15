<?php
/**
 * ACF field registration for domain entities.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers ACF hooks for the plugin.
 *
 * @return void
 */
function edu_craft_domain_register_acf_hooks() {
	add_filter( 'acf/settings/load_json', 'edu_craft_domain_register_acf_json_load_path' );
	add_filter( 'acf/json/save_paths', 'edu_craft_domain_register_acf_json_save_paths', 10, 2 );
}

/**
 * Adds plugin ACF JSON path to load locations.
 *
 * @param string[] $paths Existing load paths.
 * @return string[]
 */
function edu_craft_domain_register_acf_json_load_path( $paths ) {
	$paths[] = EDU_CRAFT_DOMAIN_PATH . 'acf-json';
	return array_values( array_unique( $paths ) );
}

/**
 * Stores Case Study domain field groups in plugin JSON path.
 *
 * @param string[] $paths Existing save paths.
 * @param array    $post  ACF field group payload.
 * @return string[]
 */
function edu_craft_domain_register_acf_json_save_paths( $paths, $post ) {
	if ( empty( $post['key'] ) ) {
		return $paths;
	}

	if ( 'group_edu_craft_case_study' !== $post['key'] ) {
		return $paths;
	}

	return array( EDU_CRAFT_DOMAIN_PATH . 'acf-json' );
}
