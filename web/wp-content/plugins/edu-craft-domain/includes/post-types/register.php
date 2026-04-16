<?php
/**
 * Post type registration module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers post type hooks.
 *
 * @return void
 */
function edu_craft_domain_register_post_types() {
	add_action( 'init', 'edu_craft_domain_register_case_study_post_type' );
}

/**
 * Registers the Case Study post type.
 *
 * @return void
 */
function edu_craft_domain_register_case_study_post_type() {
	$labels = array(
		'name'                  => __( 'Case Studies', 'edu-craft-domain' ),
		'singular_name'         => __( 'Case Study', 'edu-craft-domain' ),
		'menu_name'             => __( 'Case Studies', 'edu-craft-domain' ),
		'name_admin_bar'        => __( 'Case Study', 'edu-craft-domain' ),
		'add_new'               => __( 'Add New', 'edu-craft-domain' ),
		'add_new_item'          => __( 'Add New Case Study', 'edu-craft-domain' ),
		'new_item'              => __( 'New Case Study', 'edu-craft-domain' ),
		'edit_item'             => __( 'Edit Case Study', 'edu-craft-domain' ),
		'view_item'             => __( 'View Case Study', 'edu-craft-domain' ),
		'all_items'             => __( 'All Case Studies', 'edu-craft-domain' ),
		'search_items'          => __( 'Search Case Studies', 'edu-craft-domain' ),
		'parent_item_colon'     => __( 'Parent Case Studies:', 'edu-craft-domain' ),
		'not_found'             => __( 'No case studies found.', 'edu-craft-domain' ),
		'not_found_in_trash'    => __( 'No case studies found in Trash.', 'edu-craft-domain' ),
		'featured_image'        => __( 'Case Study Featured Image', 'edu-craft-domain' ),
		'set_featured_image'    => __( 'Set featured image', 'edu-craft-domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'edu-craft-domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'edu-craft-domain' ),
		'archives'              => __( 'Case Study Archives', 'edu-craft-domain' ),
		'insert_into_item'      => __( 'Insert into case study', 'edu-craft-domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this case study', 'edu-craft-domain' ),
		'filter_items_list'     => __( 'Filter case studies list', 'edu-craft-domain' ),
		'items_list_navigation' => __( 'Case studies list navigation', 'edu-craft-domain' ),
		'items_list'            => __( 'Case studies list', 'edu-craft-domain' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array(
			'slug' => 'case-study',
		),
		'capability_type'    => 'post',
		'has_archive'        => 'case-studies',
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-portfolio',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'case_study', $args );
}
