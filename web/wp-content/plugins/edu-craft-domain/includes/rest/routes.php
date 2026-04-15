<?php
/**
 * REST routes module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once EDU_CRAFT_DOMAIN_PATH . 'includes/rest/case-study-items.php';

/**
 * Registers REST route hooks.
 *
 * @return void
 */
function edu_craft_domain_register_rest_routes() {
	add_action( 'rest_api_init', 'edu_craft_domain_register_case_study_rest_route' );
}

/**
 * Registers Case Study archive REST endpoint.
 *
 * @return void
 */
function edu_craft_domain_register_case_study_rest_route() {
	register_rest_route(
		'edu-craft-domain/v1',
		'/case-studies',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'edu_craft_domain_rest_case_studies_callback',
			'permission_callback' => '__return_true',
			'args'                => array(
				'industry' => array(
					'description' => __( 'Industry term slug to filter by.', 'edu-craft-domain' ),
					'type'        => 'string',
					'required'    => false,
					'default'     => '',
				),
				'per_page' => array(
					'description' => __( 'Max items (1–50).', 'edu-craft-domain' ),
					'type'        => 'integer',
					'required'    => false,
					'default'     => 20,
				),
			),
		)
	);
}

/**
 * REST callback: filtered Case Study list.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function edu_craft_domain_rest_case_studies_callback( WP_REST_Request $request ) {
	$industry = $request->get_param( 'industry' );
	$per_page = (int) $request->get_param( 'per_page' );

	$data = edu_craft_domain_get_case_study_items_data( (string) $industry, $per_page );

	return new WP_REST_Response(
		array(
			'items'             => $data['items'],
			'invalid_industry'  => $data['invalid_industry'],
		),
		200
	);
}
