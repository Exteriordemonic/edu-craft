<?php
/**
 * Helpers shared by dynamic block render templates.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves the Case Study post ID for template-only dynamic blocks.
 *
 * Resolution order matches how blocks render in the Site Editor (block context),
 * the main query loop, and singular views.
 *
 * @param WP_Block|null $block Block instance passed to the render template.
 * @return int Post ID, or 0 when not a valid Case Study context.
 */
function edu_craft_get_case_study_block_post_id( $block = null ) {
	$is_case_study = static function ( $post_id ) {
		$post_id = (int) $post_id;

		return ( $post_id > 0 && 'case_study' === get_post_type( $post_id ) ) ? $post_id : 0;
	};

	if ( $block instanceof WP_Block && ! empty( $block->context['postId'] ) ) {
		$resolved = $is_case_study( $block->context['postId'] );
		if ( $resolved ) {
			return $resolved;
		}
	}

	$resolved = $is_case_study( get_the_ID() );
	if ( $resolved ) {
		return $resolved;
	}

	if ( is_singular( 'case_study' ) ) {
		return $is_case_study( get_queried_object_id() );
	}

	return 0;
}
