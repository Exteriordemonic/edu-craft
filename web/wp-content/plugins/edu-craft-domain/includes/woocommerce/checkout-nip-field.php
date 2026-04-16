<?php
/**
 * Block checkout: NIP field for B2B cart lines.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const EDU_CRAFT_DOMAIN_CHECKOUT_NIP_FIELD_ID = 'namespace/nip';
const EDU_CRAFT_DOMAIN_B2B_PRODUCT_CAT_SLUG = 'b2b';
const EDU_CRAFT_DOMAIN_NIP_DIGITS_LENGTH = 10;

/**
 * Whether the cart contains a product in the B2B category.
 *
 * @return bool
 */
function edu_craft_domain_cart_has_b2b_product() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product_id = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
		if ( $product_id && has_term( EDU_CRAFT_DOMAIN_B2B_PRODUCT_CAT_SLUG, 'product_cat', $product_id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Validates Polish NIP (10 digits + checksum).
 *
 * @param string $nip Raw or formatted NIP.
 * @return bool
 */
function edu_craft_domain_is_valid_polish_nip( $nip ) {
	$digits = preg_replace( '/\D/', '', (string) $nip );

	if ( strlen( $digits ) !== EDU_CRAFT_DOMAIN_NIP_DIGITS_LENGTH ) {
		return false;
	}

	$weights = array( 6, 5, 7, 2, 3, 4, 5, 6, 7 );
	$sum     = 0;

	for ( $i = 0; $i < 9; $i++ ) {
		$sum += (int) $digits[ $i ] * $weights[ $i ];
	}

	$checksum = $sum % 11;

	if ( 10 === $checksum ) {
		return false;
	}

	return $checksum === (int) $digits[9];
}

/**
 * Additional checkout field validate_callback (replaces WC default; must handle required + format).
 *
 * @param mixed $value Field value.
 * @param array $field Field definition.
 * @return true|WP_Error|void
 */
function edu_craft_domain_validate_checkout_nip_field( $value, array $field ) {
	$trimmed = is_string( $value ) ? trim( $value ) : $value;
	$is_empty = null === $trimmed || '' === $trimmed || ( is_string( $trimmed ) && '' === trim( $trimmed ) );

	if ( ! empty( $field['required'] ) && $is_empty ) {
		return new WP_Error(
			'woocommerce_required_checkout_field',
			sprintf(
				/* translators: %s: field id */
				__( 'The field %s is required.', 'woocommerce' ),
				$field['id']
			)
		);
	}

	if ( $is_empty ) {
		return true;
	}

	if ( ! edu_craft_domain_is_valid_polish_nip( $trimmed ) ) {
		return new WP_Error(
			'edu_craft_domain_invalid_nip',
			__( 'Nieprawidłowy numer NIP. Podaj poprawny 10-cyfrowy numer.', 'edu-craft-domain' )
		);
	}

	return true;
}

/**
 * Registers the NIP checkout field and validation.
 *
 * @return void
 */
function edu_craft_domain_register_checkout_nip_field() {
	if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
		return;
	}

	$cart_has_b2b = edu_craft_domain_cart_has_b2b_product();

	woocommerce_register_additional_checkout_field(
		array(
			'id'            => EDU_CRAFT_DOMAIN_CHECKOUT_NIP_FIELD_ID,
			'label'         => __( 'NIP', 'edu-craft-domain' ),
			'optionalLabel' => __( 'NIP (opcjonalnie)', 'edu-craft-domain' ),
			'location'      => 'order',
			'required'      => $cart_has_b2b,
			'validate_callback' => 'edu_craft_domain_validate_checkout_nip_field',
			'attributes'    => array(
				'autocomplete'     => 'tax-id',
				'aria-describedby' => 'nip-hint',
				'aria-label'       => __( 'Numer Identyfikacji Podatkowej', 'edu-craft-domain' ),
				'pattern'          => '^[0-9]{10}$',
				'title'            => __( 'Podaj 10-cyfrowy numer NIP', 'edu-craft-domain' ),
				'data-custom'      => 'nip-field',
			),
			'error'         => array(
				'id'      => 'nip-error',
				'class'   => 'woocommerce-invalid-nip',
				'message' => __( 'Nieprawidłowy numer NIP. Podaj 10-cyfrowy numer.', 'edu-craft-domain' ),
			),
		)
	);
}
