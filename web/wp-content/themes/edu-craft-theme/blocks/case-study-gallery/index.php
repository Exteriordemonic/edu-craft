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

$gallery_context = array(
	'images'  => $images_context,
	'current' => 0,
	'isOpen'  => false,
	'counter' => sprintf(
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
				<?php
				$button_context = array( 'index' => (int) $index );
				?>
				<div class="ratio ratio-4x3 rounded-2 border border-secondary-subtle overflow-hidden bg-body-secondary shadow-sm">
					<button
						type="button"
						class="cs-gallery__item btn btn-light position-absolute top-0 start-0 w-100 h-100 rounded-0 border-0 p-0 shadow-none text-reset"
						data-wp-on--click="actions.open"
						data-wp-context='<?php echo esc_attr( wp_json_encode( $button_context ) ); ?>'
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
			class="cs-lightbox__close btn-close btn-close-white position-fixed top-0 end-0 m-4"
			data-wp-on--click="actions.close"
			aria-label="<?php esc_attr_e( 'Close', 'edu-craft-theme' ); ?>"
		></button>

		<button
			type="button"
			class="cs-lightbox__nav cs-lightbox__nav--prev btn btn-outline-light rounded-circle p-2 flex-shrink-0"
			data-wp-on--click="actions.prev"
			aria-label="<?php esc_attr_e( 'Previous', 'edu-craft-theme' ); ?>"
		>
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
				<path d="M11 3 4 9l7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<div class="cs-lightbox__img-wrap d-flex align-items-center justify-content-center flex-grow-1 min-w-0 mx-2">
			<img
				class="rounded d-block mw-100"
				data-wp-bind--src="context.images[context.current].src"
				data-wp-bind--alt="context.images[context.current].alt"
				alt=""
				src=""
			/>
		</div>

		<button
			type="button"
			class="cs-lightbox__nav cs-lightbox__nav--next btn btn-outline-light rounded-circle p-2 flex-shrink-0"
			data-wp-on--click="actions.next"
			aria-label="<?php esc_attr_e( 'Next', 'edu-craft-theme' ); ?>"
		>
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
				<path d="M7 3l7 6-7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<span class="cs-lightbox__counter position-fixed bottom-0 start-50 translate-middle-x mb-4 small text-white-50" data-wp-text="context.counter"></span>
	</div>
</section>
