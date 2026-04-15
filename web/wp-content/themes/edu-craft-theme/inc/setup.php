<?php
/**
 * Theme setup.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers theme supports and navigation menus.
 *
 * @return void
 */
function edu_craft_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'dist/css/editor.css' );

	register_nav_menus(
		array(
			'primary-menu' => esc_html__( 'Primary Navigation', 'edu-craft-theme' ),
			'footer'         => esc_html__( 'Footer Navigation', 'edu-craft-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'edu_craft_theme_setup' );
