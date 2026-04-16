<?php
/**
 * #ddev-generated: Automatically generated WordPress settings file.
 * ddev manages this file and may delete or overwrite the file unless this comment is removed.
 * It is recommended that you leave this file alone.
 *
 * @package ddevapp
 */

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Authentication Unique Keys and Salts. */
define( 'AUTH_KEY', 'MtlGqjiReusQnRWEplEfDwfalIfaRMWRpkSHSywqvZJqYnqHzIzlYRSnTHIkTEsj' );
define( 'SECURE_AUTH_KEY', 'GHBqFwayDMHKQGMFytQIlXfLdbMNMrbMaAYrKxxLgPGjVfURnRVrJytgCdMqIYXa' );
define( 'LOGGED_IN_KEY', 'wqXAbClysqFADbrqvgcqDkNJnylhnBPZpaLwFHbhqFgdAaJNbjrOVcDfXREsPFYZ' );
define( 'NONCE_KEY', 'KEeBTAiPDiiRWZwGGaqCAOAyEUWMcyPHbloTfLMFRuGikqAouDlKilYSLnoaRHBC' );
define( 'AUTH_SALT', 'GOZyOJJVJApItxUizZnoRdOHRQKjNaSNCnsFBmcQbGUjxZtNjIwsuCvtbqvEoCSW' );
define( 'SECURE_AUTH_SALT', 'anCLvunbjlpxSrdtTIidBABSRWQlGFLFzIBebfVQpDRhgXXUFbDlesAspzXpWLsc' );
define( 'LOGGED_IN_SALT', 'aOECvHKudmcGKhDCoHldKTohybPAbsigmrbrLAfBmoiErNJtQRoulcjoGBFOHcXx' );
define( 'NONCE_SALT', 'CFbexIjcIpHJPaIuhQtDEVGUCFznzYVLgqfFaKSGMCmyBpZcxgTcLzsVgcdlnwtQ' );

/* Add any custom values between this line and the "stop editing" line. */

/** Domyślny locale (np. przed instalacją). Po instalacji język bierze się z bazy (Ustawienia → Ogólne) lub WP-CLI. */
define( 'WPLANG', 'pl_PL' );

/** Produkcja: db credentials — web/db.production.env lub deploy/db.production.env (patrz .example) */
if ( is_readable( __DIR__ . '/wp-config-deploy-env.php' ) ) {
	require_once __DIR__ . '/wp-config-deploy-env.php';
}

/**
 * Prefiks tabel — na produkcji nie ładujemy wp-config-ddev.php, więc musi być ustawiony tutaj.
 *
 * @global string $table_prefix
 */
if ( ! isset( $table_prefix ) || '' === $table_prefix ) {
	$table_prefix = 'wp_';
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
defined( 'ABSPATH' ) || define( 'ABSPATH', dirname( __FILE__ ) . '/' );

// Include for settings managed by ddev.
$ddev_settings = __DIR__ . '/wp-config-ddev.php';
if ( ! defined( 'DB_USER' ) && getenv( 'IS_DDEV_PROJECT' ) == 'true' && is_readable( $ddev_settings ) ) {
	require_once( $ddev_settings );
}

/** Include wp-settings.php */
if ( file_exists( ABSPATH . '/wp-settings.php' ) ) {
	require_once ABSPATH . '/wp-settings.php';
}
