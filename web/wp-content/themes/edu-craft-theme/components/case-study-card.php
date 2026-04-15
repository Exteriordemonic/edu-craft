<?php
/**
 * Case Study archive card (SSR and template shell share this markup).
 *
 * Expected variables: {@see edu_craft_render_case_study_archive_card()} in block-helpers.php.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item           = isset( $edu_craft_case_study_card_item ) ? $edu_craft_case_study_card_item : array();
$template_shell = ! empty( $edu_craft_case_study_card_template_shell );

$title     = isset( $item['title'] ) ? (string) $item['title'] : '';
$permalink = isset( $item['permalink'] ) ? (string) $item['permalink'] : '#';
$excerpt   = isset( $item['excerpt'] ) ? (string) $item['excerpt'] : '';
$thumb     = isset( $item['thumbnail'] ) && is_array( $item['thumbnail'] ) ? $item['thumbnail'] : null;
$inds      = isset( $item['industries'] ) && is_array( $item['industries'] ) ? $item['industries'] : array();

$thumb_src = ( $thumb && ! empty( $thumb['src'] ) ) ? (string) $thumb['src'] : '';
$thumb_alt = ( $thumb && isset( $thumb['alt'] ) ) ? (string) $thumb['alt'] : $title;

// Tiny transparent SVG — used only inside <template> so cloned nodes have a stable <img> for client updates.
$edu_craft_csa_img_placeholder = 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27/%3E';

$image_hidden = ( '' === $thumb_src );
$img_src      = $thumb_src;
if ( $template_shell && '' === $thumb_src ) {
	$img_src = $edu_craft_csa_img_placeholder;
}

$badges_visible = ! empty( $inds );
?>
<div class="col-12 col-md-6 col-lg-4 js-csa-card-col">
	<article class="card h-100 shadow-sm border-0 edu-craft-csa-card">
		<a
			href="<?php echo esc_url( $permalink ); ?>"
			class="ratio ratio-16x9 edu-craft-csa-card__image-link bg-light js-csa-card-image-link <?php echo $image_hidden ? 'd-none' : ''; ?>"
		>
			<?php if ( '' !== $img_src ) : ?>
				<img
					src="<?php echo esc_url( $img_src ); ?>"
					alt="<?php echo esc_attr( $thumb_alt ); ?>"
					class="card-img-top object-fit-cover h-100 rounded-top js-csa-card-img"
					loading="lazy"
					decoding="async"
				/>
			<?php endif; ?>
		</a>
		<div class="card-body d-flex flex-column">
			<h3 class="h5 card-title">
				<a href="<?php echo esc_url( $permalink ); ?>" class="stretched-link text-decoration-none text-dark js-csa-card-title-link"><?php echo esc_html( $title ); ?></a>
			</h3>
			<p class="card-text text-secondary small js-csa-card-excerpt <?php echo ( '' === $excerpt ) ? 'd-none' : ''; ?>"><?php echo esc_html( $excerpt ); ?></p>
			<div class="d-flex flex-wrap gap-2 mt-auto pt-2 js-csa-card-badges <?php echo $badges_visible ? '' : 'd-none'; ?>">
				<?php
				if ( $badges_visible ) {
					foreach ( $inds as $ind ) {
						if ( ! is_array( $ind ) ) {
							continue;
						}
						$name = isset( $ind['name'] ) ? (string) $ind['name'] : '';
						$link = isset( $ind['link'] ) ? (string) $ind['link'] : '';
						if ( '' === $name ) {
							continue;
						}
						edu_craft_render_industry_badge(
							array(
								'name'        => $name,
								'link'        => $link,
								'extra_class' => 'js-csa-card-badge',
								'tabindex'    => ( '' !== $link ) ? -1 : null,
								'show_icon'   => true,
							)
						);
					}
				}
				?>
			</div>
		</div>
	</article>
</div>
