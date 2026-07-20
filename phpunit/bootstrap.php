<?php
/**
 * PHPUnit bootstrap file
 *
 * @package OmniForm
 */

// Require composer dependencies.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'PHPUNIT_TESTING', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals

/*
 * Minimal WP_Block double for unit tests (WordPress core is not bootstrapped).
 */
if ( ! class_exists( 'WP_Block', false ) ) {
	/**
	 * Test double for WordPress WP_Block.
	 */
	class WP_Block {
		/**
		 * Block context values.
		 *
		 * @var array
		 */
		public $context = array();

		/**
		 * Original parsed array representation of block.
		 *
		 * @var array
		 */
		public $parsed_block = array();
	}
}

/*
 * Minimal REST doubles for unit tests (WordPress core is not bootstrapped).
 */
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound,Generic.Files.OneObjectStructurePerFile.MultipleFound
if ( ! class_exists( 'WP_REST_Controller', false ) ) {
	/**
	 * Test double for WordPress WP_REST_Controller.
	 */
	class WP_REST_Controller {
		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace;

		/**
		 * The rest base.
		 *
		 * @var string
		 */
		protected $rest_base;

		/**
		 * Registers the routes for the objects of the controller.
		 */
		public function register_routes() {}
	}
}

if ( ! class_exists( 'WP_REST_Server', false ) ) {
	/**
	 * Test double for WordPress WP_REST_Server.
	 */
	class WP_REST_Server {
		const READABLE   = 'GET';
		const CREATABLE  = 'POST';
		const EDITABLE   = 'POST, PUT, PATCH';
		const DELETABLE  = 'DELETE';
		const ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE';
	}
}

if ( ! class_exists( 'WP_REST_Request', false ) ) {
	/**
	 * Test double for WordPress WP_REST_Request.
	 */
	class WP_REST_Request {
		/**
		 * Get the request body.
		 *
		 * @return string
		 */
		public function get_body() {
			return '';
		}

		/**
		 * Get a header value.
		 *
		 * @param string $header Header name.
		 * @return string|null
		 */
		public function get_header( $header ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
			return null;
		}
	}
}

if ( ! class_exists( 'WP_Error', false ) ) {
	/**
	 * Test double for WordPress WP_Error.
	 */
	class WP_Error {
		/**
		 * Error code.
		 *
		 * @var string
		 */
		private $code;

		/**
		 * Error message.
		 *
		 * @var string
		 */
		private $message;

		/**
		 * Error data.
		 *
		 * @var mixed
		 */
		private $data;

		/**
		 * Constructor.
		 *
		 * @param string|int $code    Error code.
		 * @param string     $message Error message.
		 * @param mixed      $data    Error data.
		 */
		public function __construct( $code = '', $message = '', $data = '' ) {
			$this->code    = $code;
			$this->message = $message;
			$this->data    = $data;
		}

		/**
		 * Get the error code.
		 *
		 * @return string|int
		 */
		public function get_error_code() {
			return $this->code;
		}

		/**
		 * Get the error data.
		 *
		 * @return mixed
		 */
		public function get_error_data() {
			return $this->data;
		}

		/**
		 * Get the error message.
		 *
		 * @return string
		 */
		public function get_error_message() {
			return $this->message;
		}
	}
}
// phpcs:enable

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
