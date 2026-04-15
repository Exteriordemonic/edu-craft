<?php
/**
 * Example block template.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = get_field( 'heading' ) ?: __( 'Interactivity-ready ACF block', 'edu-craft-theme' );
$description = get_field( 'description' ) ?: __( 'This block demonstrates the WordPress Interactivity API with ACF Pro.', 'edu-craft-theme' );
?>

<section class="edu-craft-example js-edu-craft-example" data-wp-interactive="eduCraftExample">
	<div class="edu-craft-example__inner">
		<h3><?php echo esc_html( $heading ); ?></h3>
		<p><?php echo esc_html( $description ); ?></p>
		<button
			type="button"
			class="btn btn-primary js-edu-craft-example-toggle"
			data-wp-on--click="actions.toggleMessage"
			data-wp-bind--aria-expanded="state.isOpen"
		>
			<?php esc_html_e( 'Toggle details', 'edu-craft-theme' ); ?>
		</button>
		<p class="edu-craft-example__message" data-wp-text="state.message"></p>
	</div>
</section>
