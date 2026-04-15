<?php
/**
 * Polish NIP (tax id) normalization and validation.
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Strips non-digits from a raw NIP string.
 *
 * @param mixed $value Raw input.
 * @return string Digits only.
 */
function edu_craft_domain_normalize_nip( $value ) {
	if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
		return '';
	}
	$digits = preg_replace( '/\D/', '', (string) $value );
	return is_string( $digits ) ? $digits : '';
}

/**
 * Validates Polish NIP length, digit-only form, and weighted checksum.
 *
 * @param string $nip Normalized 10-digit string.
 * @return bool True when valid.
 */
function edu_craft_domain_is_valid_nip( $nip ) {
	if ( strlen( $nip ) !== 10 || ! ctype_digit( $nip ) ) {
		return false;
	}

	$weights = array( 6, 5, 7, 2, 3, 4, 5, 6, 7 );
	$sum     = 0;

	for ( $i = 0; $i < 9; $i++ ) {
		$sum += (int) $nip[ $i ] * $weights[ $i ];
	}

	$checksum = $sum % 11;
	if ( 10 === $checksum ) {
		$checksum = 0;
	}

	return (int) $nip[9] === $checksum;
}
