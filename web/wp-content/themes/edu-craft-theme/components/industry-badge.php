<?php
/**
 * Industry badge (pill link or span). Styles: src/scss/_industry-badge.scss.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders an industry badge; pass a `link` to render an anchor, otherwise a span.
 *
 * @param array{
 *     name?: string,
 *     link?: string,
 *     extra_class?: string,
 *     show_icon?: bool,
 *     icon_class?: string,
 *     tabindex?: int|string|false|null,
 * } $args name is required; tabindex applies to the link variant only when set (use -1 with stretched-link cards).
 * @return void
 */
function edu_craft_render_industry_badge( array $args = array() ) {
	$defaults = array(
		'name'        => '',
		'link'        => '',
		'extra_class' => '',
		'show_icon'   => false,
		'icon_class'  => '',
		'tabindex'    => null,
	);

	$badge = wp_parse_args( $args, $defaults );

	$name = (string) $badge['name'];
	if ( $name === '' ) {
		return;
	}

	$link        = (string) $badge['link'];
	$extra_class = trim( (string) $badge['extra_class'] );
	$show_icon   = ! empty( $badge['show_icon'] );
	$icon_class  = trim( (string) $badge['icon_class'] );
	$tabindex    = $badge['tabindex'];
	$classes     = trim( 'cs-industry-badge ' . $extra_class );

	if ( $link !== '' ) {
		?>
		<a
			href="<?php echo esc_url( $link ); ?>"
			class="<?php echo esc_attr( $classes ); ?>"
			<?php if ( null !== $tabindex && false !== $tabindex ) : ?>
				tabindex="<?php echo esc_attr( (string) $tabindex ); ?>"
			<?php endif; ?>
		>
			<?php
			if ( $show_icon ) {
				$icon_classes = $icon_class !== '' ? $icon_class : '';
				?>
				<svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"<?php echo $icon_classes !== '' ? ' class="' . esc_attr( $icon_classes ) . '"' : ''; ?>>
					<circle cx="5" cy="5" r="4.5" stroke="currentColor"/>
				</svg>
				<?php
			}
			echo esc_html( $name );
			?>
		</a>
		<?php
		return;
	}

	?>
	<span class="<?php echo esc_attr( $classes ); ?>">
		<?php
		if ( $show_icon ) {
			$icon_classes = $icon_class !== '' ? $icon_class : '';
			?>
			<svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"<?php echo $icon_classes !== '' ? ' class="' . esc_attr( $icon_classes ) . '"' : ''; ?>>
				<circle cx="5" cy="5" r="4.5" stroke="currentColor"/>
			</svg>
			<?php
		}
		echo esc_html( $name );
		?>
	</span>
	<?php
}
