<?php
/**
 * Respect-based submission validator adapter.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Dependencies\Respect\Validation\Exceptions\NestedValidationException;
use OmniForm\Dependencies\Respect\Validation\Rules\AllOf;
use OmniForm\Dependencies\Respect\Validation\Rules\Date;
use OmniForm\Dependencies\Respect\Validation\Rules\Email;
use OmniForm\Dependencies\Respect\Validation\Rules\HexRgbColor;
use OmniForm\Dependencies\Respect\Validation\Rules\KeyNested;
use OmniForm\Dependencies\Respect\Validation\Rules\Max;
use OmniForm\Dependencies\Respect\Validation\Rules\Min;
use OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty;
use OmniForm\Dependencies\Respect\Validation\Rules\Number;
use OmniForm\Dependencies\Respect\Validation\Rules\Optional;
use OmniForm\Dependencies\Respect\Validation\Rules\Phone;
use OmniForm\Dependencies\Respect\Validation\Rules\Time;
use OmniForm\Dependencies\Respect\Validation\Rules\Url;
use OmniForm\Dependencies\Respect\Validation\Validatable;
use OmniForm\Dependencies\Respect\Validation\Validator;
use OmniForm\Form\Field;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Submission;
use OmniForm\Form\SubmissionValidator;
use OmniForm\Form\ValidationResult;
use OmniForm\Validation\Rules\UsernameOrEmailRule;

/**
 * Validates a Submission against a FormSchema using Respect Validation.
 */
final class RespectSubmissionValidator implements SubmissionValidator {
	private const FORMAT_DATE  = 'Y-m-d';
	private const FORMAT_TIME  = 'h:i:s';
	private const FORMAT_MONTH = 'Y-m';

	/**
	 * {@inheritdoc}
	 */
	public function validate( FormSchema $schema, Submission $submission ): ValidationResult {
		try {
			$this->build_validator( $schema )->assert( $submission->values() );

			return ValidationResult::valid();
		} catch ( NestedValidationException $exception ) {
			return ValidationResult::invalid( $exception->getMessages() );
		}
	}

	/**
	 * Assemble a Respect validator from the schema's field rules.
	 *
	 * @param FormSchema $schema Form field catalog.
	 */
	private function build_validator( FormSchema $schema ): Validator {
		$validator = Validator::create();

		foreach ( $schema->fields() as $field ) {
			$rule = $this->field_rule( $field );

			if ( null === $rule ) {
				continue;
			}

			$rule->setName( $field->label() );

			$validator->addRule(
				new KeyNested(
					$field->name()->key(),
					$rule,
					$field->has_rule( 'required' )
				)
			);
		}

		return $validator;
	}

	/**
	 * Build the Respect rule tree for a field, or null when nothing to check.
	 *
	 * @param Field $field Field definition.
	 */
	private function field_rule( Field $field ): ?Validatable {
		return $this->compose( $this->constraint_parts( $field ) );
	}

	/**
	 * Ordered Respect rules for a field (presence, declared rules, implied type).
	 *
	 * @param Field $field Field definition.
	 * @return list<Validatable>
	 */
	private function constraint_parts( Field $field ): array {
		$required = $field->has_rule( 'required' );
		$parts    = array();

		if ( $required ) {
			$parts[] = new NotEmpty();
		}

		foreach ( $field->rules() as $rule_string ) {
			if ( 'required' === $rule_string ) {
				continue;
			}

			$mapped = $this->map_rule_string( $rule_string );

			if ( null === $mapped ) {
				continue;
			}

			$parts[] = $this->with_presence( $mapped, $required );
		}

		$type_rule = $this->implied_type_rule( $field );

		if ( null !== $type_rule ) {
			$parts[] = $this->with_presence( $type_rule, $required );
		}

		return $parts;
	}

	/**
	 * Map a Laravel-style rule string to a Respect rule.
	 *
	 * @param string $rule Rule string (e.g. email, min:3).
	 */
	private function map_rule_string( string $rule ): ?Validatable {
		if ( str_starts_with( $rule, 'min:' ) ) {
			return new Min( (float) substr( $rule, 4 ) );
		}

		if ( str_starts_with( $rule, 'max:' ) ) {
			return new Max( (float) substr( $rule, 4 ) );
		}

		return $this->format_rule( $rule );
	}

	/**
	 * Implied format rule for the control type, when not already declared.
	 *
	 * @param Field $field Field definition.
	 */
	private function implied_type_rule( Field $field ): ?Validatable {
		$keys = $this->format_keys_for_type( $field->type() );

		if ( array() === $keys ) {
			return null;
		}

		foreach ( $keys as $key ) {
			if ( $field->has_rule( $key ) ) {
				return null;
			}
		}

		return $this->format_rule( $keys[0] );
	}

	/**
	 * Format rule keys that satisfy a control type's format constraint.
	 * First entry is the canonical key for the implied type rule.
	 *
	 * @param string $type Control type.
	 * @return list<string>
	 */
	private function format_keys_for_type( string $type ): array {
		return match ( $type ) {
			'email' => array( 'email' ),
			'url' => array( 'url' ),
			'tel' => array( 'tel', 'phone' ),
			'number', 'range' => array( 'number', 'numeric' ),
			'date' => array( 'date' ),
			'time' => array( 'time' ),
			'month' => array( 'month' ),
			'color' => array( 'color', 'hex_color' ),
			'username-email' => array( 'username-email' ),
			default => array(),
		};
	}

	/**
	 * Map a format name (rule string or canonical type key) to a Respect rule.
	 *
	 * @param string $name Format rule name.
	 */
	private function format_rule( string $name ): ?Validatable {
		return match ( $name ) {
			'email' => new Email(),
			'url' => new Url(),
			'tel', 'phone' => new Phone(),
			'number', 'numeric' => new Number(),
			'date' => new Date( self::FORMAT_DATE ),
			'time' => new Time( self::FORMAT_TIME ),
			'month' => new Date( self::FORMAT_MONTH ),
			'color', 'hex_color' => new HexRgbColor(),
			'username-email' => new UsernameOrEmailRule(),
			default => null,
		};
	}

	/**
	 * Optional rules skip empty values; required rules do not.
	 *
	 * @param Validatable $rule     Rule to wrap.
	 * @param bool        $required Whether the field is required.
	 */
	private function with_presence( Validatable $rule, bool $required ): Validatable {
		return $required ? $rule : new Optional( $rule );
	}

	/**
	 * Combine rule parts into a single Validatable, or null when empty.
	 *
	 * @param array $parts Rule parts to combine.
	 */
	private function compose( array $parts ): ?Validatable {
		if ( array() === $parts ) {
			return null;
		}

		if ( 1 === count( $parts ) ) {
			return $parts[0];
		}

		return new AllOf( ...$parts );
	}
}
