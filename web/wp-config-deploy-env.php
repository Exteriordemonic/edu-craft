<?php
/**
 * Produkcja: wczytuje deploy/db.production.env (KEY=value) i definiuje DB_*.
 * Lokalnie DDEV — plik jest pomijany (credentials z wp-config-ddev.php).
 *
 * Ładowany wyłącznie z wp-config.php (przed define ABSPATH).
 *
 * @package edu-craft
 */

if ( getenv( 'IS_DDEV_PROJECT' ) === 'true' ) {
	return;
}

if ( defined( 'DB_USER' ) ) {
	return;
}

$env_path = dirname( __DIR__ ) . '/deploy/db.production.env';
if ( ! is_readable( $env_path ) ) {
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
