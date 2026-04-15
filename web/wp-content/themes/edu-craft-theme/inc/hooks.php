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
 * Core passes `false` or `''` when the `loading` attribute should be omitted (for example the
 * first in-viewport images that may get `fetchpriority="high"`). Treating those as empty via
 * `empty()` incorrectly forced `lazy` and triggered _doing_it_wrong in WP 6.3+.
 *
 * @param string|bool $value   Proposed `loading` value, `true` for default, or falsey to omit.
 * @param string      $image   Full `img` HTML tag.
 * @param string      $context Filter context.
 * @return string|bool
 */
function edu_craft_lazy_load_images( $value, $image, $context ) {
	if ( false === $value || '' === $value ) {
		return $value;
	}

	if ( true === $value ) {
		return 'lazy';
	}

	return $value;
}
add_filter( 'wp_img_tag_add_loading_attr', 'edu_craft_lazy_load_images' );
