<?php
/**
 * ACF integration.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers local ACF fields for theme blocks.
 *
 * @return void
 */
function edu_craft_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_edu_craft_example',
			'title'                 => 'Edu Craft Example Block',
			'fields'                => array(
				array(
					'key'           => 'field_edu_craft_heading',
					'label'         => 'Heading',
					'name'          => 'heading',
					'type'          => 'text',
					'default_value' => 'Interactivity-ready ACF block',
				),
				array(
					'key'           => 'field_edu_craft_description',
					'label'         => 'Description',
					'name'          => 'description',
					'type'          => 'textarea',
					'rows'          => 4,
					'default_value' => 'This block demonstrates the WordPress Interactivity API with ACF Pro.',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/edu-craft-example',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'edu_craft_register_acf_fields' );
