<?php
/**
 * Test double for WordPress WP_Error.
 *
 * WordPress core is not bootstrapped for unit tests, so a minimal WP_Error
 * is provided here for code that constructs or asserts against WP_Error.
 *
 * @package OmniForm
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

if ( ! class_exists( 'WP_Error', false ) ) {
	/**
	 * Test double for WordPress WP_Error.
	 */
	class WP_Error {
		/**
		 * Error code.
		 *
		 * @var string|int
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
		 * @param mixed      $data    Optional error data.
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
		 * Get the error message.
		 *
		 * @param string|int $code Optional error code. Unused; this double holds a single error.
		 * @return string
		 */
		public function get_error_message( $code = '' ) {
			return $this->message;
		}

		/**
		 * Get the error data.
		 *
		 * @param string|int $code Optional error code. Unused; this double holds a single error.
		 * @return mixed
		 */
		public function get_error_data( $code = '' ) {
			return $this->data;
		}
	}
}
