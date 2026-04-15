<?php
/**
 * Case Study archive listing (filters + results).
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
$terms        = isset( $archive_data['terms'] ) && is_array( $archive_data['terms'] ) ? $archive_data['terms'] : array();
$items        = isset( $archive_data['items'] ) && is_array( $archive_data['items'] ) ? $archive_data['items'] : array();
?>
<div class="edu-craft-case-study-archive" data-wp-interactive="eduCraftCaseStudyArchive">
	<?php edu_craft_case_study_base_loop_the_template(); ?>

	<div class="case-study-archive-filter mb-4">
		<p class="small text-secondary mb-2"><?php esc_html_e( 'Filter by industry', 'edu-craft-theme' ); ?></p>
		<div class="d-flex flex-wrap gap-2 align-items-center">
			<button
				type="button"
				class="btn btn-sm"
				data-wp-context="<?php echo esc_attr( wp_json_encode( array( 'slug' => '' ) ) ); ?>"
				data-wp-class--btn-primary="state.activeIndustry === context.slug"
				data-wp-class--btn-outline-secondary="state.activeIndustry !== context.slug"
				data-wp-on--click="actions.selectIndustry"
			><?php esc_html_e( 'All', 'edu-craft-theme' ); ?></button>
			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( ! is_array( $term ) || empty( $term['slug'] ) ) {
					continue;
				}
				?>
			<button
				type="button"
				class="btn btn-sm"
				data-wp-context="<?php echo esc_attr( wp_json_encode( array( 'slug' => (string) $term['slug'] ) ) ); ?>"
				data-wp-class--btn-primary="state.activeIndustry === context.slug"
				data-wp-class--btn-outline-secondary="state.activeIndustry !== context.slug"
				data-wp-on--click="actions.selectIndustry"
			><?php echo esc_html( isset( $term['name'] ) ? (string) $term['name'] : '' ); ?></button>
			<?php endforeach; ?>
		</div>
	</div>

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
				<?php edu_craft_case_study_base_loop_the_items( $items ); ?>
			</div>
		</div>

		<p
			class="text-secondary py-5 text-center mb-0 edu-craft-csa-empty"
			data-wp-class--d-none="state.invalidIndustry || state.items.length > 0 || state.isLoading"
		><?php esc_html_e( 'No case studies yet.', 'edu-craft-theme' ); ?></p>
	</div>
</div>
