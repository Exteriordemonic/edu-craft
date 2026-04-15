<?php
/**
 * Shared hooks.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds loading optimization to non-critical images.
 *
 * @param string $attr Existing image loading attribute.
 * @return string
 */
function edu_craft_lazy_load_images( $attr ) {
	if ( empty( $attr ) ) {
		return 'lazy';
	}

	return $attr;
}
add_filter( 'wp_img_tag_add_loading_attr', 'edu_craft_lazy_load_images' );
