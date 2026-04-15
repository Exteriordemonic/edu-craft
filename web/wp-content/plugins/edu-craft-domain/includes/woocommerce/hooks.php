<?php
/**
 * WooCommerce hooks module.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers WooCommerce hooks.
 *
 * @return void
 */
function edu_craft_domain_register_woocommerce_hooks() {
	require_once EDU_CRAFT_DOMAIN_PATH . 'includes/woocommerce/nip-validator.php';
	require_once EDU_CRAFT_DOMAIN_PATH . 'includes/woocommerce/checkout-nip.php';
	edu_craft_domain_boot_checkout_nip();
}
