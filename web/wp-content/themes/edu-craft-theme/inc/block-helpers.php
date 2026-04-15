<?php
/**
 * Helpers shared by dynamic block render templates.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once EDU_CRAFT_THEME_PATH . 'components/industry-badge.php';

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

/**
 * Loads Case Study archive data once and prints Interactivity state.
 *
 * @return array{items: array<int, array>, invalid_industry: bool, terms: array<int, array{slug: string, name: string}>}
 */
function edu_craft_case_study_archive_prepare() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	if ( ! is_post_type_archive( 'case_study' ) ) {
		return array(
			'items'             => array(),
			'invalid_industry'  => false,
			'terms'             => array(),
		);
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only view state for archive.
	$industry_raw = isset( $_GET['industry'] ) ? sanitize_title( wp_unslash( $_GET['industry'] ) ) : '';

	$items_data = array(
		'items'             => array(),
		'invalid_industry'  => false,
	);

	if ( function_exists( 'edu_craft_domain_get_case_study_items_data' ) ) {
		$items_data = edu_craft_domain_get_case_study_items_data( $industry_raw, 20 );
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'industry',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	$terms_out = array();
	foreach ( $terms as $t ) {
		$terms_out[] = array(
			'slug' => $t->slug,
			'name' => $t->name,
		);
	}

	$archive_url = get_post_type_archive_link( 'case_study' );
	if ( ! $archive_url ) {
		$archive_url = home_url( '/' );
	}

	wp_interactivity_state(
		'eduCraftCaseStudyArchive',
		array(
			'items'             => array_values( $items_data['items'] ),
			'activeIndustry'    => $industry_raw,
			'invalidIndustry'   => ! empty( $items_data['invalid_industry'] ),
			'isLoading'         => false,
			'restUrl'           => rest_url( 'edu-craft-domain/v1/case-studies' ),
			'nonce'             => wp_create_nonce( 'wp_rest' ),
			'archiveUrl'        => $archive_url,
			'terms'             => $terms_out,
			'queryParam'        => 'industry',
		)
	);

	$cache = array(
		'items'             => array_values( $items_data['items'] ),
		'invalid_industry'  => ! empty( $items_data['invalid_industry'] ),
		'terms'             => $terms_out,
	);
	return $cache;
}

/**
 * Prints Interactivity state once (DRY entry point).
 *
 * @return void
 */
function edu_craft_case_study_archive_bootstrap_store() {
	edu_craft_case_study_archive_prepare();
}

/**
 * Renders a Case Study card for archive lists (SSR, or shell inside the HTML template element when $template_shell is true).
 *
 * @param array<string,mixed> $item Item data (REST/plugin shape).
 * @param bool                $template_shell When true, renders the clone source used by {@see edu_craft_case_study_base_loop_the_template()}.
 * @return void
 */
function edu_craft_render_case_study_archive_card( array $item, $template_shell = false ) {
	$edu_craft_case_study_card_item           = $item;
	$edu_craft_case_study_card_template_shell = (bool) $template_shell;
	require EDU_CRAFT_THEME_PATH . 'components/case-study-card.php';
}

require_once EDU_CRAFT_THEME_PATH . 'components/case-study-base-loop.php';
