<?php
/**
 * Case Study hero dynamic block template.
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

$short_description = get_field( 'short_description', $post_id );
$industries        = get_the_terms( $post_id, 'industry' );
?>

<section class="case-study-hero mb-5">

	<div class="case-study-hero__media position-relative rounded overflow-hidden mb-4">
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'case-study-hero__image img-fluid w-100 object-fit-cover' ) ); ?>
		<?php endif; ?>
		<div class="case-study-hero__overlay position-absolute bottom-0 start-0 end-0 px-4 pt-5 pb-3">
			<?php the_title( '<h1 class="h3 text-white mb-0 fw-medium">', '</h1>' ); ?>
		</div>
	</div>

	<?php if ( ! empty( $short_description ) ) : ?>
		<p class="lead text-secondary mb-3"><?php echo esc_html( $short_description ); ?></p>
	<?php endif; ?>

	<?php if ( ! empty( $industries ) && ! is_wp_error( $industries ) ) : ?>
		<div class="d-flex flex-wrap gap-2">
			<?php
			foreach ( $industries as $industry ) :
				$term_link = get_term_link( $industry );
				if ( is_wp_error( $term_link ) ) {
					continue;
				}
				edu_craft_render_industry_badge(
					array(
						'name'        => $industry->name,
						'link'        => $term_link,
						'show_icon'   => true,
						'icon_class'  => 'case-study-hero__industry-icon',
					)
				);
			endforeach;
			?>
		</div>
	<?php endif; ?>

</section>