<?php
/**
 * Case Study archive results block template.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_post_type_archive( 'case_study' ) ) {
	return;
}

$archive_data = edu_craft_case_study_archive_prepare();
$items        = isset( $archive_data['items'] ) && is_array( $archive_data['items'] ) ? $archive_data['items'] : array();
?>

	<div class="case-study-archive-results position-relative">
		<p
			class="alert alert-warning mb-4"
			data-wp-class--d-none="state.invalidIndustry === false"
			role="status"
		><?php esc_html_e( 'No case studies match this industry filter.', 'edu-craft-theme' ); ?></p>

		<div
			class="position-absolute top-50 start-50 translate-middle w-100 text-center py-5 edu-craft-csa-loading"
			data-wp-class--d-none="state.isLoading === false"
			aria-live="polite"
		>
			<span class="spinner-border text-primary" role="status"><span class="visually-hidden"><?php esc_html_e( 'Loading', 'edu-craft-theme' ); ?></span></span>
		</div>

		<div data-wp-class--opacity-25="state.isLoading">
			<div class="row g-4" id="edu-craft-csa-grid">
				<?php
				foreach ( $items as $item ) {
					if ( is_array( $item ) ) {
						edu_craft_render_case_study_archive_card( $item );
					}
				}
				?>
			</div>
		</div>

		<p
			class="text-secondary py-5 text-center mb-0 edu-craft-csa-empty"
			data-wp-class--d-none="state.invalidIndustry || state.items.length > 0 || state.isLoading"
		><?php esc_html_e( 'No case studies yet.', 'edu-craft-theme' ); ?></p>
	</div>
</div>
