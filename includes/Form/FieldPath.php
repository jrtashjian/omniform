<?php
/**
 * Field path value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable multi-segment field location (fieldset prefix + control name).
 *
 * Used for submission keys and HTML name attributes after composition.
 */
final class FieldPath {
	/**
	 * @param list<string> $segments Sanitized path segments.
	 */
	private function __construct(
		private readonly array $segments
	) {}

	/**
	 * Empty path (no fieldset prefix yet).
	 */
	public static function empty(): self {
		return new self( array() );
	}

	/**
	 * Create from segments (each sanitized).
	 *
	 * @param list<string> $segments Path segments.
	 *
	 * @throws \InvalidArgumentException If empty or any segment sanitizes to empty.
	 */
	public static function from_segments( array $segments ): self {
		if ( array() === $segments ) {
			throw new \InvalidArgumentException( 'Field path requires at least one segment.' );
		}

		$clean = array();

		foreach ( $segments as $segment ) {
			if ( ! is_string( $segment ) ) {
				throw new \InvalidArgumentException( 'Field path segments must be strings.' );
			}

			$sanitized = FieldName::sanitize( $segment );

			if ( '' === $sanitized ) {
				throw new \InvalidArgumentException( 'Field path segment sanitation resulted in an empty string.' );
			}

			$clean[] = $sanitized;
		}

		return new self( $clean );
	}

	/**
	 * Path consisting of a single control name.
	 */
	public static function root( FieldName $name ): self {
		return new self( array( $name->value() ) );
	}

	/**
	 * Append a control name segment.
	 */
	public function append( FieldName $name ): self {
		return new self( array( ...$this->segments, $name->value() ) );
	}

	/**
	 * Whether this path has no segments.
	 */
	public function is_empty(): bool {
		return array() === $this->segments;
	}

	/**
	 * Dot-separated key (e.g. contact.email).
	 *
	 * @throws \InvalidArgumentException If the path is empty.
	 */
	public function key(): string {
		$this->assert_not_empty();

		return implode( '.', $this->segments );
	}

	/**
	 * HTML name attribute (e.g. contact[email] or contact[email][]).
	 *
	 * @throws \InvalidArgumentException If the path is empty.
	 */
	public function html_name( bool $multiple = false ): string {
		$this->assert_not_empty();

		$name = $this->segments[0] . implode(
			'',
			array_map(
				static fn( string $segment ): string => '[' . $segment . ']',
				array_slice( $this->segments, 1 )
			)
		);

		if ( $multiple ) {
			$name .= '[]';
		}

		return $name;
	}

	/**
	 * @return list<string>
	 */
	public function segments(): array {
		return $this->segments;
	}

	/**
	 * @throws \InvalidArgumentException If the path is empty.
	 */
	private function assert_not_empty(): void {
		if ( $this->is_empty() ) {
			throw new \InvalidArgumentException( 'Field path is empty.' );
		}
	}
}
