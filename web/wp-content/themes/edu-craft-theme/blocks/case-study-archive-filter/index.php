<?php
/**
 * Case Study archive filter block template.
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
?>
<div class="edu-craft-case-study-archive" data-wp-interactive="eduCraftCaseStudyArchive">
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
