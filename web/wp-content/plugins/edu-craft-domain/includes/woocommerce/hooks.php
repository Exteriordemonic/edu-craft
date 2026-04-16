<?php
/**
 * WooCommerce hooks module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/checkout-nip-field.php';

/**
 * Registers WooCommerce hooks.
 *
 * @return void
 */
function edu_craft_domain_register_woocommerce_hooks() {
	edu_craft_domain_register_checkout_nip_field();
}
