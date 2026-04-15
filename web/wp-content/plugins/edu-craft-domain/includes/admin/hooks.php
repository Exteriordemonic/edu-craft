<?php
/**
 * Admin hooks module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs NIP on the classic order edit screen when stored on the order.
 *
 * @param \WC_Order $order Order object.
 * @return void
 */
function edu_craft_domain_render_order_nip_in_admin( $order ) {
	if ( ! $order instanceof \WC_Order || ! function_exists( 'edu_craft_domain_get_order_nip_meta_key' ) ) {
		return;
	}

	$nip = $order->get_meta( edu_craft_domain_get_order_nip_meta_key(), true );
	if ( '' === $nip || null === $nip ) {
		return;
	}

	echo '<div class="address"><p><strong>' . esc_html__( 'NIP (B2B)', 'edu-craft-domain' ) . ':</strong> ' . esc_html( (string) $nip ) . '</p></div>';
}

/**
 * Registers admin hooks.
 *
 * @return void
 */
function edu_craft_domain_register_admin_hooks() {
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'edu_craft_domain_render_order_nip_in_admin', 10, 1 );
}
