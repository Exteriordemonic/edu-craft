<?php
/**
 * Case Study meta dynamic block template.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = edu_craft_get_case_study_block_post_id( $block ?? null );

if ( ! $post_id ) {
	return;
}

$client_name = get_field( 'client', $post_id );
$client_url  = get_field( 'client_url', $post_id );
$industries  = get_the_terms( $post_id, 'industry' );

$has_industries = ! empty( $industries ) && ! is_wp_error( $industries );
$visible_rows   = array_filter(
	array(
		'client'    => ! empty( $client_name ),
		'industry'  => $has_industries,
		'clientUrl' => ! empty( $client_url ),
	)
);

$row_index = 0;
$row_total = count( $visible_rows );
?>

<aside class="cs-meta-card">
	<h2 class="cs-meta-card__title"><?php esc_html_e( 'Case Study Details', 'edu-craft-theme' ); ?></h2>

	<?php if ( ! empty( $client_name ) ) : ?>
		<?php
		++$row_index;
		$row_class = ( $row_index === $row_total ) ? ' cs-meta-card__row--last' : '';
		?>
		<div class="cs-meta-card__row<?php echo esc_attr( $row_class ); ?>">
			<span class="cs-meta-card__label"><?php esc_html_e( 'Client', 'edu-craft-theme' ); ?></span>
			<span class="cs-meta-card__value"><?php echo esc_html( $client_name ); ?></span>
		</div>
	<?php endif; ?>

	<?php if ( $has_industries ) : ?>
		<?php
		++$row_index;
		$row_class = ( $row_index === $row_total ) ? ' cs-meta-card__row--last' : '';
		?>
		<div class="cs-meta-card__row<?php echo esc_attr( $row_class ); ?>">
			<span class="cs-meta-card__label"><?php esc_html_e( 'Industry', 'edu-craft-theme' ); ?></span>
			<div class="cs-meta-card__badges">
				<?php
				foreach ( $industries as $industry ) :
					$term_link = get_term_link( $industry );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}
					edu_craft_render_industry_badge(
						array(
							'name'      => $industry->name,
							'link'      => $term_link,
							'show_icon' => true,
						)
					);
				endforeach;
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $client_url ) ) : ?>
		<?php
		++$row_index;
		$row_class = ( $row_index === $row_total ) ? ' cs-meta-card__row--last' : '';
		?>
		<div class="cs-meta-card__row<?php echo esc_attr( $row_class ); ?>">
			<span class="cs-meta-card__label"><?php esc_html_e( 'Client URL', 'edu-craft-theme' ); ?></span>
			<a class="cs-meta-card__link" href="<?php echo esc_url( $client_url ); ?>" target="_blank" rel="noopener noreferrer">
				<span class="cs-meta-card__value"><?php echo esc_html( $client_url ); ?></span>
			</a>
		</div>
	<?php endif; ?>
</aside>