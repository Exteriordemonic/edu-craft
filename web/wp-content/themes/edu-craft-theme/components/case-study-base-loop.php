<?php
/**
 * Case Study archive listing: PHP loop + HTML template element for client-side cloning.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Empty item shape for the card template (same keys as REST/SSR items).
 *
 * @return array<string, mixed>
 */
function edu_craft_case_study_base_loop_shell_item() {
	return array(
		'title'      => '',
		'permalink'  => '#',
		'excerpt'    => '',
		'thumbnail'  => array(
			'src' => '',
			'alt' => '',
		),
		'industries' => array(),
	);
}

/**
 * Renders cards for preloaded posts (SSR).
 *
 * @param array<int, array<string, mixed>> $items Item list from {@see edu_craft_case_study_archive_prepare()}.
 * @return void
 */
function edu_craft_case_study_base_loop_the_items( array $items ) {
	foreach ( $items as $item ) {
		if ( is_array( $item ) ) {
			edu_craft_render_case_study_archive_card( $item );
		}
	}
}

/**
 * Prints a single card template element used to build cards after REST filter (see script.js).
 *
 * @return void
 */
function edu_craft_case_study_base_loop_the_template() {
	?>
	<template id="edu-craft-csa-card-template">
		<?php edu_craft_render_case_study_archive_card( edu_craft_case_study_base_loop_shell_item(), true ); ?>
	</template>
	<?php
}
