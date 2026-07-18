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
	 * Constructor.
	 *
	 * @param array $rules Rule strings.
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
	 * All validation rule strings.
	 *
	 * @return list<string>
	 */
	public function all(): array {
		return $this->rules;
	}

	/**
	 * Whether a rule is present.
	 *
	 * Matches "required" or "min:3" by name.
	 *
	 * @param string $name Rule name.
	 *
	 * @return bool
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
