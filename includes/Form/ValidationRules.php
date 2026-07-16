<?php
/**
 * Validation rules value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable list of Laravel-style validation rule strings.
 */
final class ValidationRules {
	/**
	 * @param list<string> $rules Rule strings (e.g. required, min:3).
	 *
	 * @throws \InvalidArgumentException If any rule is not a non-empty string.
	 */
	public function __construct(
		private readonly array $rules = array(),
	) {
		foreach ( $this->rules as $rule ) {
			if ( ! is_string( $rule ) || '' === $rule ) {
				throw new \InvalidArgumentException( 'Validation rules must be non-empty strings.' );
			}
		}
	}

	/**
	 * @return list<string>
	 */
	public function all(): array {
		return $this->rules;
	}

	/**
	 * Whether a rule is present (matches "required" or "min:3" by name).
	 */
	public function has( string $name ): bool {
		foreach ( $this->rules as $rule ) {
			if ( $rule === $name || str_starts_with( $rule, $name . ':' ) ) {
				return true;
			}
		}

		return false;
	}
}
