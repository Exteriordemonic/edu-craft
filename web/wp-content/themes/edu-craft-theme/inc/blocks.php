<?php
/**
 * Block registration.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all blocks from /blocks directory.
 *
 * @return void
 */
function edu_craft_register_theme_blocks() {
	$block_directories = glob( EDU_CRAFT_THEME_PATH . 'blocks/*', GLOB_ONLYDIR );

	if ( empty( $block_directories ) ) {
		return;
	}

	foreach ( $block_directories as $block_directory ) {
		$block_json_path = $block_directory . '/block.json';

		if ( ! file_exists( $block_json_path ) ) {
			continue;
		}

		register_block_type( $block_directory );

		$block_functions_file = $block_directory . '/functions.php';
		if ( file_exists( $block_functions_file ) ) {
			require_once $block_functions_file;
		}
	}
}
add_action( 'init', 'edu_craft_register_theme_blocks' );
