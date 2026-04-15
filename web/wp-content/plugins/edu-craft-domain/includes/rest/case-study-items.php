<?php
/**
 * Case Study archive query and REST response helpers.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates an industry term slug and returns the term or null.
 *
 * @param string $slug Industry slug (may be empty).
 * @return WP_Term|null
 */
function edu_craft_domain_resolve_industry_term( $slug ) {
	$slug = is_string( $slug ) ? sanitize_title( $slug ) : '';

	if ( '' === $slug ) {
		return null;
	}

	$term = get_term_by( 'slug', $slug, 'industry' );

	if ( ! $term || is_wp_error( $term ) ) {
		return null;
	}

	return $term;
}

/**
 * Runs the Case Study query for archive / REST (DRY domain logic).
 *
 * @param string $industry_slug Optional industry taxonomy slug; empty string = all published posts.
 * @param int    $per_page      Max posts (capped).
 * @return WP_Query
 */
function edu_craft_domain_query_case_studies( $industry_slug = '', $per_page = 20 ) {
	$per_page = min( max( (int) $per_page, 1 ), 50 );

	$args = array(
		'post_type'           => 'case_study',
		'post_status'         => 'publish',
		'posts_per_page'      => $per_page,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	$term = edu_craft_domain_resolve_industry_term( $industry_slug );

	if ( $term ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'industry',
				'field'    => 'term_id',
				'terms'    => array( (int) $term->term_id ),
			),
		);
	}

	return new WP_Query( $args );
}

/**
 * Returns true when slug is non-empty but not a valid industry term (invalid filter).
 *
 * @param string $industry_slug Raw slug from request.
 * @return bool
 */
function edu_craft_domain_is_invalid_industry_slug( $industry_slug ) {
	if ( ! is_string( $industry_slug ) || '' === $industry_slug ) {
		return false;
	}

	return null === edu_craft_domain_resolve_industry_term( $industry_slug );
}

/**
 * Maps a WP_Post to REST-friendly item data (presentation-agnostic fields).
 *
 * @param WP_Post $post Post object.
 * @return array<string,mixed>
 */
function edu_craft_domain_format_case_study_item( $post ) {
	if ( ! $post instanceof WP_Post ) {
		return array();
	}

	$post_id = (int) $post->ID;
	$excerpt = $post->post_excerpt;

	if ( function_exists( 'get_field' ) ) {
		$short = get_field( 'short_description', $post_id );
		if ( is_string( $short ) && '' !== trim( $short ) ) {
			$excerpt = $short;
		}
	}

	$thumb_id  = get_post_thumbnail_id( $post_id );
	$thumbnail = null;

	if ( $thumb_id ) {
		$thumb_src = wp_get_attachment_image_src( $thumb_id, 'medium_large' );
		if ( $thumb_src ) {
			$thumbnail = array(
				'src' => $thumb_src[0],
				'alt' => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id ),
			);
		}
	}

	$industries_raw = get_the_terms( $post_id, 'industry' );
	$industries     = array();

	if ( ! empty( $industries_raw ) && ! is_wp_error( $industries_raw ) ) {
		foreach ( $industries_raw as $term ) {
			$link = get_term_link( $term );
			$industries[] = array(
				'name' => $term->name,
				'slug' => $term->slug,
				'link' => is_wp_error( $link ) ? '' : $link,
			);
		}
	}

	return array(
		'id'          => $post_id,
		'title'       => get_the_title( $post_id ),
		'permalink'   => get_permalink( $post_id ),
		'excerpt'     => wp_strip_all_tags( wp_trim_words( $excerpt, 40, '…' ) ),
		'thumbnail'   => $thumbnail,
		'industries'  => $industries,
	);
}

/**
 * Runs query and formats items for REST / consumers.
 *
 * @param string $industry_slug Optional industry slug.
 * @param int    $per_page      Max items.
 * @return array{ items: array<int,array>, invalid_industry: bool }
 */
function edu_craft_domain_get_case_study_items_data( $industry_slug = '', $per_page = 20 ) {
	$raw_slug = is_string( $industry_slug ) ? $industry_slug : '';
	$invalid  = edu_craft_domain_is_invalid_industry_slug( $raw_slug );

	if ( $invalid ) {
		return array(
			'items'             => array(),
			'invalid_industry'  => true,
		);
	}

	$sanitized = sanitize_title( $raw_slug );
	$query     = edu_craft_domain_query_case_studies( $sanitized, $per_page );

	$items = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$items[] = edu_craft_domain_format_case_study_item( $post );
		}
	}


	return array(
		'items'             => $items,
		'invalid_industry'  => false,
	);
}
