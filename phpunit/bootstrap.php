<?php
/**
 * PHPUnit bootstrap file
 *
 * @package OmniForm
 */

// Require composer dependencies.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'PHPUNIT_TESTING', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals

// Initialize WP_Mock.
WP_Mock::bootstrap();

// Suppress deprecation errors from vendor dependencies.
set_error_handler( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
	function ( $errno, $errstr, $errfile ) {
		if ( E_DEPRECATED === $errno && false !== strpos( $errfile, '/vendor/' ) ) {
			return true; // Suppress the error.
		}
		return false; // Let default handler deal with it.
	}
);

// Activate strict mode.
WP_Mock::activateStrictMode();
