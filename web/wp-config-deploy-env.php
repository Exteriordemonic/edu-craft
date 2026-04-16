<?php
/**
 * Produkcja: wczytuje plik KEY=value z DB_*.
 *
 * Kolejność ścieżek (open_basedir na MyDevil często obejmuje tylko katalog web — wtedy
 * deploy/db.production.env poza web jest nieczytelny):
 * 1) web/db.production.env (zalecane na hostingu)
 * 2) ../deploy/db.production.env (lokalny backup / dev)
 *
 * Lokalnie DDEV — pomijane (wp-config-ddev.php).
 *
 * @package edu-craft
 */

if ( getenv( 'IS_DDEV_PROJECT' ) === 'true' ) {
	return;
}

if ( defined( 'DB_USER' ) ) {
	return;
}

$candidates = array(
	__DIR__ . '/db.production.env',
	dirname( __DIR__ ) . '/deploy/db.production.env',
);
$env_path = null;
foreach ( $candidates as $path ) {
	if ( is_readable( $path ) ) {
		$env_path = $path;
		break;
	}
}
if ( ! $env_path ) {
	return;
}

$allowed = array( 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST' );
$lines   = file( $env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
if ( ! is_array( $lines ) ) {
	return;
}

foreach ( $lines as $line ) {
	$line = trim( $line );
	if ( $line === '' || str_starts_with( $line, '#' ) ) {
		continue;
	}
	if ( ! str_contains( $line, '=' ) ) {
		continue;
	}
	list( $key, $value ) = explode( '=', $line, 2 );
	$key   = trim( $key );
	$value = trim( $value );
	if ( ! in_array( $key, $allowed, true ) ) {
		continue;
	}
	$value = trim( $value, " \t\n\r\0\x0B\"'" );
	if ( ! defined( $key ) ) {
		define( $key, $value );
	}
}
