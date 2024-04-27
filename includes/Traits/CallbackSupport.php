<?php
/**
 * The CallbackSupport trait.
 *
 * @package OmniForm
 */

namespace OmniForm\Traits;

/**
 * The CallbackSupport trait.
 */
trait CallbackSupport {
	/**
	 * Check if a callback exists in the given string.
	 *
	 * @param string $content The content to check.
	 *
	 * @return bool
	 */
	protected function has_callback( $content ) {
		return false !== strpos( $content, '{{' );
	}

	/**
	 * Find all callback strings in the given content and replace them with their results.
	 *
	 * @param string $content The content to process.
	 *
	 * @return string
	 */
	protected function process_callbacks( $content ) {
		// Match callback placeholders like "{{ callback_function }}".
		$pattern = '/{{\s*([^}]+)\s*}}/';

		// Replace all callback placeholders with their results.
		return preg_replace_callback(
			$pattern,
			function ( $matches ) {
				$callback = trim( $matches[1] );

				// Ensure the function exists before calling.
				if ( ! function_exists( $callback ) ) {
					return '';
				}

				$result = $callback();

				if ( is_bool( $result ) ) {
					$result = strval( intval( $result ) );
				}

				if ( is_numeric( $result ) ) {
					$result = strval( $result );
				}

				if ( ! is_string( $result ) ) {
					return '';
				}

				return $result;
			},
			$content
		);
	}
}
