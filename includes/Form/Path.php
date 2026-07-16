<?php
/**
 * Path value object for form field paths.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable value object representing a path to a form field.
 */
final class Path {
	/**
	 * Create a new Path instance.
	 *
	 * @param array<string> $segments The segments of the path.
	 */
	private function __construct(
		private readonly array $segments
	) {}

	/**
	 * Create Path from segments.
	 *
	 * @param array $segments The segments of the path.
	 *
	 * @return self
	 * @throws \InvalidArgumentException If empty or non-string segments.
	 */
	public static function from_segments( array $segments ): self {
		if ( array() === $segments ) {
			throw new \InvalidArgumentException( 'Path requires at least one segment.' );
		}

		$clean = array();

		foreach ( $segments as $segment ) {
			if ( ! is_string( $segment ) ) {
				throw new \InvalidArgumentException( 'Path segments must be strings.' );
			}

			$sanitized = self::sanitize( $segment );

			if ( '' === $sanitized ) {
				throw new \InvalidArgumentException( 'Path segment sanitation resulted in an empty string.' );
			}

			$clean[] = $sanitized;
		}

		return new self( $clean );
	}

	/**
	 * Get dot-separated key.
	 *
	 * @return string
	 */
	public function key(): string {
		return implode( '.', $this->segments );
	}

	/**
	 * Get HTML name attribute.
	 *
	 * @param bool $multiple Append [] for multiple.
	 * @return string
	 */
	public function html_name( bool $multiple = false ): string {
		$name = $this->segments[0] . implode(
			'',
			array_map(
				fn( $segment ) => "[$segment]",
				array_slice( $this->segments, 1 )
			)
		);

		if ( $multiple ) {
			$name .= '[]';
		}

		return $name;
	}

	/**
	 * Get segments.
	 *
	 * @return array<string>
	 */
	public function segments(): array {
		return $this->segments;
	}

	/**
	 * Sanitize a path segment.
	 *
	 * @param string $segment The segment to sanitize.
	 * @return string
	 */
	private static function sanitize( string $segment ): string {
		// Remove any percent-encoded characters.
		$segment = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $segment );
		// Remove any characters that are not alphanumeric, underscore, or hyphen.
		$segment = preg_replace( '/[^a-zA-Z0-9_-]/', '', $segment );

		return $segment;
	}
}
