<?php
/**
 * Field name value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable single-segment control name.
 *
 * This is the field's own name attribute (or label fallback), not the full
 * fieldset path. Compose with FieldPath via ControlName for HTML/submission names.
 */
final class FieldName {
	/**
	 * Private constructor — use named constructors.
	 *
	 * @param string $value Sanitized single segment.
	 */
	private function __construct(
		private readonly string $value
	) {}

	/**
	 * Create from a raw name string.
	 *
	 * @param string $raw Unsanitized name string.
	 *
	 * @throws \InvalidArgumentException If sanitation yields an empty string.
	 */
	public static function of( string $raw ): self {
		$sanitized = self::sanitize( $raw );

		if ( '' === $sanitized ) {
			throw new \InvalidArgumentException( 'Field name cannot be empty after sanitation.' );
		}

		return new self( $sanitized );
	}

	/**
	 * Prefer an explicit name attribute, otherwise infer from the label.
	 *
	 * @param ?string $name  Explicit name attribute, or null to fall back to label.
	 * @param string  $label Human-readable label used as fallback.
	 *
	 * @throws \InvalidArgumentException If both resolve to an empty segment.
	 */
	public static function from_name_or_label( ?string $name, string $label ): self {
		$raw = ( null !== $name && '' !== $name ) ? $name : $label;

		return self::of( $raw );
	}

	/**
	 * Sanitize a raw name or label into a single segment.
	 *
	 * Spaces become hyphens; percent-encoded and non [A-Za-z0-9_-] characters are stripped.
	 *
	 * @param string $name Raw name or label to sanitize.
	 */
	public static function sanitize( string $name ): string {
		$name = preg_replace( '/\s+/', '-', $name );
		$name = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $name );
		$name = preg_replace( '/[^A-Za-z0-9_-]/', '', $name );

		return $name;
	}

	/**
	 * Sanitized segment value.
	 */
	public function value(): string {
		return $this->value;
	}
}
