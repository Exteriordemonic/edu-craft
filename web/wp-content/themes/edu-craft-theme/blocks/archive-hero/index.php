<?php
/**
 * Archive hero — title and optional intro for archive views.
 *
 * @package edu-craft-theme
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks (unused).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title_attr = isset( $attributes['title'] ) ? (string) $attributes['title'] : '';
$desc_attr  = isset( $attributes['description'] ) ? (string) $attributes['description'] : '';
$level      = isset( $attributes['headingLevel'] ) ? (int) $attributes['headingLevel'] : 1;
$level      = min( 6, max( 1, $level ) );

$title_text = '' !== trim( $title_attr )
	? $title_attr
	: wp_strip_all_tags( (string) get_the_archive_title() );

if ( '' === $title_text ) {
	$title_text = __( 'Archives', 'edu-craft-theme' );
}

$description = '' !== trim( $desc_attr )
	? $desc_attr
	: get_the_archive_description();

$tag = 'h' . $level;

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'archive-hero mb-4 mb-lg-5',
	)
);
?>
<section <?php echo $wrapper; ?>>
	<div class="archive-hero__inner">
		<<?php echo esc_attr( $tag ); ?> class="archive-hero__title display-6 fw-medium text-body mb-0">
			<?php echo esc_html( $title_text ); ?>
		</<?php echo esc_attr( $tag ); ?>>

		<?php if ( is_string( $description ) && '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
			<div class="archive-hero__description lead text-secondary mt-3 mb-0">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
