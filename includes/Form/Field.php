<?php
/**
 * Form field definition.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable definition of a single form field.
 */
final class Field {
	/**
	 * Validation rules for the field.
	 *
	 * @var ValidationRules
	 */
	private readonly ValidationRules $rules;

	/**
	 * Constructor.
	 *
	 * @param FieldPath     $name  Composed field path (submission / HTML name identity).
	 * @param string        $label Human-readable label.
	 * @param string        $type  Control type (text, email, file, radio, …).
	 * @param array<string> $rules Laravel-style validation rules.
	 *
	 * @throws \InvalidArgumentException If label, type, path, or a rule is empty.
	 */
	public function __construct(
		private readonly FieldPath $name,
		private readonly string $label,
		private readonly string $type,
		array $rules = array(),
	) {
		if ( '' === $this->label ) {
			throw new \InvalidArgumentException( 'Field label cannot be empty.' );
		}

		if ( '' === $this->type ) {
			throw new \InvalidArgumentException( 'Field type cannot be empty.' );
		}

		if ( $this->name->is_empty() ) {
			throw new \InvalidArgumentException( 'Field name path cannot be empty.' );
		}

		$this->rules = new ValidationRules( $rules );
	}

	/**
	 * Composed field path.
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
	 * Control type.
	 */
	public function type(): string {
		return $this->type;
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
	 *
	 * @param string $name Rule name to check (e.g. "required", "min:3").
	 */
	public function has_rule( string $name ): bool {
		return $this->rules->has( $name );
	}
}
