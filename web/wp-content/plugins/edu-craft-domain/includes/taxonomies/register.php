<?php
/**
 * Taxonomy registration module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers taxonomy hooks.
 *
 * @return void
 */
function edu_craft_domain_register_taxonomies() {
	add_action( 'init', 'edu_craft_domain_register_industry_taxonomy' );
	add_filter( 'term_link', 'edu_craft_domain_industry_term_link_to_case_study_archive', 10, 3 );
}

/**
 * Points industry term permalinks to the case study archive with an industry query arg.
 *
 * @param string $termlink Term link URL.
 * @param mixed  $term    Term object or ID.
 * @param string $taxonomy Taxonomy slug.
 * @return string
 */
function edu_craft_domain_industry_term_link_to_case_study_archive( $termlink, $term, $taxonomy ) {
	if ( 'industry' !== $taxonomy ) {
		return $termlink;
	}

	$archive = get_post_type_archive_link( 'case_study' );
	if ( ! $archive ) {
		return $termlink;
	}

	$slug = '';
	if ( is_object( $term ) && isset( $term->slug ) ) {
		$slug = (string) $term->slug;
	} elseif ( is_numeric( $term ) ) {
		$resolved = get_term( (int) $term, 'industry' );
		if ( $resolved instanceof WP_Term && ! is_wp_error( $resolved ) ) {
			$slug = (string) $resolved->slug;
		}
	}

	if ( '' === $slug ) {
		return $termlink;
	}

	return add_query_arg( 'industry', $slug, $archive );
}

/**
 * Registers the Industry taxonomy.
 *
 * @return void
 */
function edu_craft_domain_register_industry_taxonomy() {
	$labels = array(
		'name'              => __( 'Industries', 'edu-craft-domain' ),
		'singular_name'     => __( 'Industry', 'edu-craft-domain' ),
		'search_items'      => __( 'Search Industries', 'edu-craft-domain' ),
		'all_items'         => __( 'All Industries', 'edu-craft-domain' ),
		'parent_item'       => __( 'Parent Industry', 'edu-craft-domain' ),
		'parent_item_colon' => __( 'Parent Industry:', 'edu-craft-domain' ),
		'edit_item'         => __( 'Edit Industry', 'edu-craft-domain' ),
		'update_item'       => __( 'Update Industry', 'edu-craft-domain' ),
		'add_new_item'      => __( 'Add New Industry', 'edu-craft-domain' ),
		'new_item_name'     => __( 'New Industry Name', 'edu-craft-domain' ),
		'menu_name'         => __( 'Industries', 'edu-craft-domain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => 'industry',
		'rewrite'           => array(
			'slug' => 'industry',
		),
		'show_in_rest'      => true,
	);

	register_taxonomy( 'industry', array( 'case_study' ), $args );
}
