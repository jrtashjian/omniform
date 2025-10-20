<?php
/**
 * PHPUnit bootstrap file
 *
 * @package OmniForm
 */

// Require composer dependencies.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Initialize WP_Mock.
WP_Mock::bootstrap();

// Activate strict mode.
WP_Mock::activateStrictMode();

// Load callback functions for tests.
require_once __DIR__ . '/includes/callback-functions.php';
