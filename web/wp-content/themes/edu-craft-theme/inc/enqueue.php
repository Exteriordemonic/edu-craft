<?php
/**
 * Asset loading.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns asset version based on modification date.
 *
 * @param string $relative_path Relative file path inside theme.
 * @return string
 */
function edu_craft_get_asset_version( $relative_path ) {
	$absolute_path = EDU_CRAFT_THEME_PATH . ltrim( $relative_path, '/' );

	if ( file_exists( $absolute_path ) ) {
		return (string) filemtime( $absolute_path );
	}

	return EDU_CRAFT_THEME_VERSION;
}

/**
 * Enqueues theme frontend assets.
 *
 * @return void
 */
function edu_craft_enqueue_assets() {
	$style_path  = 'dist/css/main.css';
	$script_path = 'dist/js/main.js';

	if ( file_exists( EDU_CRAFT_THEME_PATH . $style_path ) ) {
		wp_enqueue_style(
			'edu-craft-theme-main',
			EDU_CRAFT_THEME_URI . $style_path,
			array(),
			edu_craft_get_asset_version( $style_path )
		);
	}

	if ( file_exists( EDU_CRAFT_THEME_PATH . $script_path ) ) {
		wp_enqueue_script(
			'edu-craft-theme-main',
			EDU_CRAFT_THEME_URI . $script_path,
			array(),
			edu_craft_get_asset_version( $script_path ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'edu_craft_enqueue_assets' );

/**
 * Enqueues editor-only assets.
 *
 * @return void
 */
function edu_craft_enqueue_editor_assets() {
	$editor_style_path  = 'dist/css/editor.css';
	$editor_script_path = 'dist/js/editor.js';

	if ( file_exists( EDU_CRAFT_THEME_PATH . $editor_style_path ) ) {
		wp_enqueue_style(
			'edu-craft-theme-editor',
			EDU_CRAFT_THEME_URI . $editor_style_path,
			array( 'wp-edit-blocks' ),
			edu_craft_get_asset_version( $editor_style_path )
		);
	}

	if ( file_exists( EDU_CRAFT_THEME_PATH . $editor_script_path ) ) {
		wp_enqueue_script(
			'edu-craft-theme-editor',
			EDU_CRAFT_THEME_URI . $editor_script_path,
			array( 'wp-blocks', 'wp-element', 'wp-edit-post' ),
			edu_craft_get_asset_version( $editor_script_path ),
			true
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'edu_craft_enqueue_editor_assets' );
