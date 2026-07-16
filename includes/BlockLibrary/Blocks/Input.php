<?php
/**
 * The Input block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;
use OmniForm\Form\Path;
use OmniForm\Validation\Rules\UsernameOrEmailRule;

/**
 * The Input block class.
 */
class Input extends BaseControlBlock {
	const FORMAT_DATE           = 'Y-m-d';
	const FORMAT_TIME           = 'h:i:s';
	const FORMAT_MONTH          = 'Y-m';
	const FORMAT_WEEK           = 'Y-\WW';
	const FORMAT_DATETIME_LOCAL = 'Y-m-d H:i:s';

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control(): string {
		return sprintf(
			'<input %s />',
			get_block_wrapper_attributes( $this->get_extra_wrapper_attributes() )
		);
	}

	/**
	 * Gets the input type for the field.
	 *
	 * @return string
	 */
	protected function get_type(): string {
		$field_type = $this->get_block_attribute( 'fieldType' ) ?? 'text';

		switch ( $field_type ) {
			case 'username-email':
				return 'text';
			default:
				return $field_type;
		}
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function get_extra_wrapper_attributes(): array {
		$extra_attributes = wp_parse_args(
			array(
				'type'       => $this->get_type(),
				'aria-label' => esc_attr( wp_strip_all_tags( $this->get_field_label() ?? '' ) ),
			),
			parent::get_extra_wrapper_attributes()
		);

		$attribute_input_type_mapping = array(
			'placeholder' => array( 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden', 'username-email' ),
			'min'         => array( 'range' ),
			'max'         => array( 'range' ),
			'step'        => array( 'range' ),
		);

		foreach ( $attribute_input_type_mapping as $attribute => $input_type ) {
			if ( in_array( $this->get_type(), $input_type, true ) && $this->get_block_attribute( 'field' . ucfirst( $attribute ) ) ) {
				$extra_attributes[ $attribute ] = $this->get_block_attribute( 'field' . ucfirst( $attribute ) );
			}
		}

		return array_filter( $extra_attributes );
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function get_control_value(): string {
		if ( $this->get_block_attribute( 'fieldValue' ) ) {
			return parent::get_control_value();
		}

		return match ( $this->get_block_attribute( 'fieldType' ) ) {
			// Checkboxes default to boolean. However, when transforming from a select field we want the option name to be the value.
			'checkbox',
			// Radios are always grouped so value is its name.
			'radio' => $this->get_field_label() ?? '',
			// Date and time inputs need a default value to display properly on iOS.
			'date' => gmdate( self::FORMAT_DATE ),
			'time' => gmdate( self::FORMAT_TIME ),
			'month' => gmdate( self::FORMAT_MONTH ),
			'week' => gmdate( self::FORMAT_WEEK ),
			'datetime-local' => gmdate( self::FORMAT_DATETIME_LOCAL ),
			'search' => (string) get_query_var( $this->get_control_name() ),
			default => '',
		};
	}

	/**
	 * Is the field required?
	 */
	public function is_required(): bool {
		return ! in_array( $this->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true ) && parent::is_required();
	}

	/**
	 * Gets the validation rules for the field.
	 *
	 * @return list<object>
	 */
	public function get_validation_rules(): array {
		$rules = parent::get_validation_rules();

		$validation_mapping = array(
			'email'          => new Validation\Rules\Email(),
			'url'            => new Validation\Rules\Url(),
			'tel'            => new Validation\Rules\Phone(),
			'number'         => new Validation\Rules\Number(),
			'date'           => new Validation\Rules\Date( self::FORMAT_DATE ),
			'time'           => new Validation\Rules\Time( self::FORMAT_TIME ),
			'month'          => new Validation\Rules\Date( self::FORMAT_MONTH ),
			'username-email' => new UsernameOrEmailRule(),
			'range'          => new Validation\Rules\Number(),
			'color'          => new Validation\Rules\HexRgbColor(),
		);

		$field_type = $this->get_block_attribute( 'fieldType' );

		if ( isset( $validation_mapping[ $field_type ] ) ) {
			$rule    = $validation_mapping[ $field_type ];
			$rules[] = $this->is_required()
				? $rule
				: new Validation\Rules\Optional( $rule );
		}

		return $rules;
	}

	/**
	 * Gets the control's name parts.
	 *
	 * @return list<string>
	 */
	public function get_control_name_parts(): array {
		$control_name_parts = parent::get_control_name_parts();

		if ( in_array( $this->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true ) && $this->get_block_context( 'omniform/isChoiceGroup' ) ) {
			array_pop( $control_name_parts );
		}

		return $control_name_parts;
	}

	/**
	 * Gets the control's name attribute.
	 */
	public function get_control_name(): string {
		return Path::from_segments( $this->get_control_name_parts() )
			->html_name( 'checkbox' === $this->get_block_attribute( 'fieldType' ) && $this->get_block_context( 'omniform/isChoiceGroup' ) );
	}
}
