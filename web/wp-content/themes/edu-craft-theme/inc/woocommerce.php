<?php
/**
 * WooCommerce integration.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds WooCommerce support.
 *
 * @return void
 */
function edu_craft_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'edu_craft_add_woocommerce_support' );

/**
 * Outputs a product loop helper label.
 *
 * @return void
 */
function edu_craft_product_loop_label() {
	echo '<p class="edu-craft-product-label">' . esc_html__( 'Quality checked for educators', 'edu-craft-theme' ) . '</p>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'edu_craft_product_loop_label', 15 );

/**
 * Enqueues WooCommerce JS enhancements.
 *
 * @return void
 */
function edu_craft_enqueue_woocommerce_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$woo_script_path = 'dist/js/woocommerce.js';
	if ( file_exists( EDU_CRAFT_THEME_PATH . $woo_script_path ) ) {
		wp_enqueue_script(
			'edu-craft-theme-woocommerce',
			EDU_CRAFT_THEME_URI . $woo_script_path,
			array(),
			edu_craft_get_asset_version( $woo_script_path ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'edu_craft_enqueue_woocommerce_assets' );
