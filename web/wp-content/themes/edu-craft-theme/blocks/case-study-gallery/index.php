<?php
/**
 * Case Study gallery dynamic block template.
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

$gallery_images = get_field( 'gallery', $post_id );

if ( empty( $gallery_images ) || ! is_array( $gallery_images ) ) {
	return;
}

$image_ids = array_values(
	array_filter(
		array_map(
			static function ( $gallery_image ) {
				return isset( $gallery_image['ID'] ) ? (int) $gallery_image['ID'] : 0;
			},
			$gallery_images
		)
	)
);

if ( empty( $image_ids ) ) {
	return;
}

$images_context = array_map(
	static function ( $image_id ) {
		$image_id = (int) $image_id;

		return array(
			'src' => wp_get_attachment_image_url( $image_id, 'full' ),
			'alt' => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
		);
	},
	$image_ids
);

$images_context = array_values(
	array_filter(
		$images_context,
		static function ( $image ) {
			return ! empty( $image['src'] );
		}
	)
);

if ( empty( $images_context ) ) {
	return;
}

$first_image = $images_context[0];

$gallery_context = array(
	'images'       => $images_context,
	'current'      => 0,
	'isOpen'       => false,
	'lightboxSrc'  => $first_image['src'] ?? '',
	'lightboxAlt'  => $first_image['alt'] ?? '',
	'counter'      => sprintf(
		/* translators: 1: current image index, 2: total image count. */
		__( '%1$d / %2$d', 'edu-craft-theme' ),
		1,
		count( $images_context )
	),
);
?>

<section
	class="mt-5"
	data-wp-interactive="edu-craft/case-study-gallery"
	data-wp-context='<?php echo esc_attr( wp_json_encode( $gallery_context ) ); ?>'
>
	<h2 class="h4 mb-4 fw-medium"><?php esc_html_e( 'Gallery', 'edu-craft-theme' ); ?></h2>
	<div class="row g-3">
		<?php foreach ( $image_ids as $index => $image_id ) : ?>
			<div class="col-12 col-md-6 col-lg-4">
				<div class="ratio ratio-4x3 rounded-2 border border-secondary-subtle overflow-hidden bg-body-secondary shadow-sm">
					<button
						type="button"
						class="cs-gallery__item btn btn-light position-absolute top-0 start-0 w-100 h-100 rounded-0 border-0 p-0 shadow-none text-reset"
						data-wp-on--click="actions.open"
						data-index="<?php echo esc_attr( (string) $index ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Open image %d', 'edu-craft-theme' ), $index + 1 ) ); ?>"
					>
						<?php
						echo wp_get_attachment_image(
							$image_id,
							'large',
							false,
							array(
								'class'   => 'w-100 h-100 object-fit-cover',
								'loading' => 'lazy',
								'alt'     => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
							)
						);
						?>
					</button>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div
		class="cs-lightbox position-fixed top-0 start-0 w-100 h-100 flex-row align-items-center justify-content-center gap-2 p-0 m-0 bg-dark bg-opacity-75"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Image viewer', 'edu-craft-theme' ); ?>"
		data-wp-class--is-open="context.isOpen"
		data-wp-on--keydown="actions.onKeydown"
	>
		<button
			type="button"
			class="cs-lightbox__close btn btn-light rounded-circle shadow d-inline-flex align-items-center justify-content-center p-2 p-md-3 position-fixed top-0 end-0 m-3 m-md-4 border-0"
			data-wp-on--click="actions.close"
			aria-label="<?php esc_attr_e( 'Close', 'edu-craft-theme' ); ?>"
		>
			<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="d-block text-dark" viewBox="0 0 16 16" aria-hidden="true">
				<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
			</svg>
		</button>

		<button
			type="button"
			class="cs-lightbox__nav cs-lightbox__nav--prev btn btn-light rounded-circle shadow d-inline-flex align-items-center justify-content-center flex-shrink-0 p-3 mx-1 mx-md-2 border-0"
			data-wp-on--click="actions.prev"
			aria-label="<?php esc_attr_e( 'Previous', 'edu-craft-theme' ); ?>"
		>
			<svg width="22" height="22" viewBox="0 0 18 18" fill="none" class="d-block text-dark" aria-hidden="true">
				<path d="M11 3 4 9l7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<div class="cs-lightbox__img-wrap d-flex align-items-center justify-content-center flex-grow-1 min-w-0 mx-2">
			<img
				class="rounded d-block mw-100"
				data-wp-bind--src="context.lightboxSrc"
				data-wp-bind--alt="context.lightboxAlt"
				alt="<?php echo esc_attr( $first_image['alt'] ?? '' ); ?>"
				src="<?php echo esc_url( $first_image['src'] ?? '' ); ?>"
			/>
		</div>

		<button
			type="button"
			class="cs-lightbox__nav cs-lightbox__nav--next btn btn-light rounded-circle shadow d-inline-flex align-items-center justify-content-center flex-shrink-0 p-3 mx-1 mx-md-2 border-0"
			data-wp-on--click="actions.next"
			aria-label="<?php esc_attr_e( 'Next', 'edu-craft-theme' ); ?>"
		>
			<svg width="22" height="22" viewBox="0 0 18 18" fill="none" class="d-block text-dark" aria-hidden="true">
				<path d="M7 3l7 6-7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<span class="cs-lightbox__counter position-fixed bottom-0 start-50 translate-middle-x mb-4 small text-white-50" data-wp-text="context.counter"></span>
	</div>
</section>
