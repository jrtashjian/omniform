<?php
/**
 * Form field group definition.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable definition of a fieldset / field group.
 */
final class FieldGroup {
	/**
	 * @var ValidationRules
	 */
	private readonly ValidationRules $rules;

	/**
	 * @param FieldPath    $name         Composed group path.
	 * @param string       $label        Human-readable label.
	 * @param list<string> $rules        Laravel-style validation rules.
	 * @param bool         $choice_group Whether children share one value (radio/checkbox group).
	 *
	 * @throws \InvalidArgumentException If label, path, or a rule is empty.
	 */
	public function __construct(
		private readonly FieldPath $name,
		private readonly string $label,
		array $rules = array(),
		private readonly bool $choice_group = false,
	) {
		if ( '' === $this->label ) {
			throw new \InvalidArgumentException( 'Field group label cannot be empty.' );
		}

		if ( $this->name->is_empty() ) {
			throw new \InvalidArgumentException( 'Field group name path cannot be empty.' );
		}

		$this->rules = new ValidationRules( $rules );
	}

	/**
	 * Composed group path.
	 */
	public function name(): FieldPath {
		return $this->name;
	}

	/**
	 * Human-readable label.
	 */
	public function label(): string {
		return $this->label;
	}

	/**
	 * Validation rules.
	 *
	 * @return list<string>
	 */
	public function rules(): array {
		return $this->rules->all();
	}

	/**
	 * Whether a rule is present (matches "required" or "min:3" by name).
	 */
	public function has_rule( string $name ): bool {
		return $this->rules->has( $name );
	}

	/**
	 * Whether this group is a radio/checkbox choice group.
	 */
	public function is_choice_group(): bool {
		return $this->choice_group;
	}

	/**
	 * @return array{name: string, label: string, rules: list<string>, choice_group: bool}
	 */
	public function to_array(): array {
		return array(
			'name'         => $this->name->key(),
			'label'        => $this->label,
			'rules'        => $this->rules->all(),
			'choice_group' => $this->choice_group,
		);
	}

	/**
	 * @param array<string, mixed> $data Serialized group.
	 *
	 * @throws \InvalidArgumentException If the payload is invalid.
	 */
	public static function from_array( array $data ): self {
		return new self(
			name: FieldPath::from_segments( explode( '.', (string) $data['name'] ) ),
			label: (string) $data['label'],
			rules: self::validate_rules( $data['rules'] ?? array() ),
			choice_group: (bool) ( $data['choice_group'] ?? false ),
		);
	}

	/**
	 * @param mixed $raw Raw rules list.
	 * @return list<string>
	 */
	private static function validate_rules( mixed $raw ): array {
		if ( ! is_array( $raw ) ) {
			return array();
		}

		return array_values( array_filter(
			$raw,
			static function ( mixed $rule ): bool {
				if ( ! is_string( $rule ) || '' === $rule ) {
					throw new \InvalidArgumentException( 'Response rules must be non-empty strings.' );
				}
				return true;
			},
		) );
	}
}
