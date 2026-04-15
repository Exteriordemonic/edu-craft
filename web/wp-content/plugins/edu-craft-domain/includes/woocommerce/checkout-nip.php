<?php
/**
 * WooCommerce block checkout: B2B NIP field and Store API cart flag.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product category slug for B2B training products (SPEC / demo content).
 *
 * @var string
 */
const EDU_CRAFT_DOMAIN_B2B_PRODUCT_CAT_SLUG = 'b2b';

/**
 * Additional checkout field id (namespace/name).
 *
 * @var string
 */
const EDU_CRAFT_DOMAIN_NIP_FIELD_ID = 'edu-craft-domain/nip';

/**
 * Store API cart extension namespace (must match extensions key).
 *
 * @var string
 */
const EDU_CRAFT_DOMAIN_CART_EXTENSION_NAMESPACE = 'edu-craft-domain';

/**
 * Returns true when the cart contains a product in the B2B category.
 *
 * @param \WC_Cart $cart Cart instance.
 * @return bool
 */
function edu_craft_domain_cart_requires_nip( \WC_Cart $cart ) {
	foreach ( $cart->get_cart() as $item ) {
		if ( empty( $item['data'] ) || ! $item['data'] instanceof \WC_Product ) {
			continue;
		}
		$product = $item['data'];
		$post_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		if ( $post_id && has_term( EDU_CRAFT_DOMAIN_B2B_PRODUCT_CAT_SLUG, 'product_cat', $post_id ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Registers Store API cart extension data for checkout document rules.
 *
 * @return void
 */
function edu_craft_domain_register_cart_nip_extension() {
	if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
		return;
	}

	woocommerce_store_api_register_endpoint_data(
		array(
			'endpoint'        => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER,
			'namespace'       => EDU_CRAFT_DOMAIN_CART_EXTENSION_NAMESPACE,
			'schema_type'     => ARRAY_A,
			'schema_callback' => static function () {
				return array(
					'requires_nip' => array(
						'description' => __( 'True when the cart contains a B2B product and NIP is required at checkout.', 'edu-craft-domain' ),
						'type'        => 'boolean',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
				);
			},
			'data_callback'   => static function () {
				if ( ! function_exists( 'WC' ) || ! WC()->cart instanceof \WC_Cart ) {
					return array( 'requires_nip' => false );
				}
				return array(
					'requires_nip' => edu_craft_domain_cart_requires_nip( WC()->cart ),
				);
			},
		)
	);
}

/**
 * JSON-schema rules: field required when NIP is required.
 *
 * @return array<string, mixed>
 */
function edu_craft_domain_nip_field_required_rules() {
	$ns = EDU_CRAFT_DOMAIN_CART_EXTENSION_NAMESPACE;
	return array(
		'cart' => array(
			'type'       => 'object',
			'properties' => array(
				'extensions' => array(
					'type'       => 'object',
					'properties' => array(
						$ns => array(
							'type'       => 'object',
							'properties' => array(
								'requires_nip' => array( 'const' => true ),
							),
							'required'     => array( 'requires_nip' ),
						),
					),
					'required'     => array( $ns ),
				),
			),
			'required'     => array( 'extensions' ),
		),
	);
}

/**
 * Sanitizes NIP input (digits only, max 10).
 *
 * @param mixed  $value Field value.
 * @param array  $field Field definition.
 * @return string
 */
function edu_craft_domain_sanitize_checkout_nip( $value, $field ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$digits = edu_craft_domain_normalize_nip( $value );
	if ( strlen( $digits ) > 10 ) {
		$digits = substr( $digits, 0, 10 );
	}
	return $digits;
}

/**
 * Validates NIP depending on cart B2B state.
 *
 * @param mixed $value Field value.
 * @param array $field Field definition.
 * @return bool|\WP_Error
 */
function edu_craft_domain_validate_checkout_nip( $value, $field ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$cart = function_exists( 'WC' ) ? WC()->cart : null;
	if ( ! $cart instanceof \WC_Cart ) {
		return true;
	}

	$requires = edu_craft_domain_cart_requires_nip( $cart );
	$digits   = edu_craft_domain_normalize_nip( $value );

	if ( ! $requires ) {
		if ( '' === $digits ) {
			return true;
		}
		if ( edu_craft_domain_is_valid_nip( $digits ) ) {
			return true;
		}
		return new WP_Error(
			'edu_craft_domain_nip_invalid_optional',
			__( 'Please enter a valid NIP or leave the field empty.', 'edu-craft-domain' )
		);
	}

	if ( '' === $digits ) {
		return new WP_Error(
			'edu_craft_domain_nip_required',
			__( 'NIP is required for orders that include B2B products.', 'edu-craft-domain' )
		);
	}

	if ( ! edu_craft_domain_is_valid_nip( $digits ) ) {
		return new WP_Error(
			'edu_craft_domain_nip_invalid',
			__( 'Please enter a valid Polish NIP (10 digits, correct checksum).', 'edu-craft-domain' )
		);
	}

	return true;
}

/**
 * Registers block checkout additional field (after Blocks package loads).
 *
 * @return void
 */
function edu_craft_domain_register_block_checkout_nip_field() {
	if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
		return;
	}

	woocommerce_register_additional_checkout_field(
		array(
			'id'                => EDU_CRAFT_DOMAIN_NIP_FIELD_ID,
			'label'             => __( 'NIP', 'edu-craft-domain' ),
			'optionalLabel'     => __( 'NIP (optional)', 'edu-craft-domain' ),
			'location'          => 'order',
			'type'              => 'text',
			'hidden'            => false,
			'required'          => edu_craft_domain_nip_field_required_rules(),
			'attributes'        => array(
				'maxLength'     => 13,
				'title'         => __( 'Polish tax identification number (NIP)', 'edu-craft-domain' ),
				'autocomplete'  => 'off',
			),
			'sanitize_callback' => 'edu_craft_domain_sanitize_checkout_nip',
			'validate_callback' => 'edu_craft_domain_validate_checkout_nip',
		)
	);
}

/**
 * Order meta key for persisted NIP (Woo additional field, order group).
 *
 * @return string
 */
function edu_craft_domain_get_order_nip_meta_key() {
	return '_wc_other/' . EDU_CRAFT_DOMAIN_NIP_FIELD_ID;
}

/**
 * Boots checkout NIP integration (Store API + block checkout field).
 *
 * @return void
 */
function edu_craft_domain_boot_checkout_nip() {
	add_action( 'woocommerce_init', 'edu_craft_domain_register_cart_nip_extension', 20 );
	// Blocks may load on plugins_loaded before our init runs; register immediately if that hook already fired.
	if ( did_action( 'woocommerce_blocks_loaded' ) ) {
		edu_craft_domain_register_block_checkout_nip_field();
	} else {
		add_action( 'woocommerce_blocks_loaded', 'edu_craft_domain_register_block_checkout_nip_field', 5 );
	}
}
