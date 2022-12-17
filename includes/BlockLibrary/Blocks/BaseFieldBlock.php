<?php
/**
 * The BaseFieldBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;
use OmniForm\Plugin\FormIngestionEngine;

/**
 * The BaseFieldBlock block class.
 */
abstract class BaseFieldBlock extends BaseBlock {
	use HasColors;

	/**
	 * The Form Injestion Engine
	 *
	 * @var FormIngestionEngine
	 */
	protected $injestion;

	/**
	 * The input's generated name.
	 *
	 * @var string
	 */
	protected $field_name;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

		$this->injestion = omniform()->get( FormIngestionEngine::class );

		$this->field_name = empty( $this->getBlockAttribute( 'fieldName' ) )
			? sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) )
			: $this->getBlockAttribute( 'fieldName' );

		if ( $this->isHiddenInput() ) {
			return sprintf(
				'<input type="hidden" %s />',
				$this->getControlName() . $this->getControlValue()
			);
		}

		$attributes = array(
			$this->blockClasses(),
			$this->getColorStyles( $this->attributes ),
		);

		return sprintf(
			'<div %s>%s</div>',
			implode( ' ', $attributes ),
			$this->renderFieldLabel() . $this->renderField() . $this->renderFieldError()
		);
	}

	/**
	 * Determine if the field type is a text input.
	 *
	 * @return bool
	 */
	protected function isTextInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' )
		);
	}

	/**
	 * Determine if the field type is a checbox or radio.
	 *
	 * @return bool
	 */
	protected function isOptionInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'checkbox', 'radio' )
		);
	}

	/**
	 * Determine if the field type is a hidden input.
	 *
	 * @return bool
	 */
	protected function isHiddenInput() {
		return 'hidden' === $this->getBlockAttribute( 'fieldType' );
	}

	/**
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderFieldLabel() {
		return empty( $this->getBlockAttribute( 'fieldLabel' ) ) ? '' : sprintf(
			'<label class="omniform-field-label" for="%s">%s</label>',
			esc_attr( $this->field_name ),
			wp_kses_post( $this->getBlockAttribute( 'fieldLabel' ) )
		);
	}

	/**
	 * Render the input's error text element.
	 *
	 * @return string
	 */
	protected function renderFieldError() {
		$errors = $this->injestion->fieldError( $this->field_name );
		return empty( $errors ) ? '' : sprintf(
			'<p class="omniform-field-support" style="color:red;">%s</p>',
			wp_kses_post( $errors )
		);
	}

	/**
	 * Generate the class="" attribute.
	 *
	 * @return string
	 */
	protected function blockClasses() {
		$classes = array(
			// Standard block type class.
			'wp-block-omniform-' . $this->blockTypeName(),
			// Apply custom class for each field type.
			empty( $this->getBlockAttribute( 'fieldType' ) )
				? 'omniform-' . $this->blockTypeName()
				: 'omniform-field-' . $this->getBlockAttribute( 'fieldType' ),
			$this->getBlockAttribute( 'isRequired' )
				? 'field-required'
				: '',
			// Supports classes.
			$this->getColorClasses( $this->attributes ),
		);

		return sprintf(
			'class="%s"',
			esc_attr( trim( implode( ' ', $classes ) ) )
		);
	}

	/**
	 * Generate key="value" attributes for control.
	 *
	 * @return string
	 */
	protected function getControlAttributes() {
		return trim(
			implode(
				' ',
				array(
					$this->getControlId(),
					$this->getControlName(),
					$this->getControlPlaceholder(),
					$this->getControlValue(),
					$this->getControlSelected(),
				)
			)
		);
	}

	/**
	 * Generate the id="" attribute.
	 *
	 * @return string
	 */
	protected function getControlId() {
		return sprintf(
			'id="%s"',
			esc_attr( sanitize_title( $this->field_name ) )
		);
	}

	/**
	 * Generate the name="" attribute.
	 *
	 * @return string
	 */
	protected function getControlName() {
		// Nest form data within a fieldset.
		if ( $this->getBlockContext( 'omniform/fieldGroupName' ) ) {
			$input_name = 'radio' === $this->getBlockAttribute( 'fieldType' )
				? $this->getBlockContext( 'omniform/fieldGroupName' )
				: $this->getBlockContext( 'omniform/fieldGroupName' ) . '[' . $this->field_name . ']';
		}

		return sprintf(
			'name="%s"',
			esc_attr( $input_name ?? $this->field_name )
		);
	}

	/**
	 * Generate the value="" attribute.
	 *
	 * @return string
	 */
	protected function getControlValue() {
		$default_value = $this->isOptionInput()
			? $this->field_name
			: $this->getBlockAttribute( 'fieldValue' );

		if ( 'checkbox' === $this->getBlockAttribute( 'fieldType' ) ) {
			$default_value = true;
		}

		$submitted_value = 'radio' === $this->getBlockAttribute( 'fieldType' )
			? $default_value
			: $this->injestion->formValue(
				$this->field_name,
				$this->getBlockContext( 'omniform/fieldGroupName' )
			);

		return sprintf(
			'value="%s"',
			$submitted_value && ! $this->isHiddenInput()
				? esc_attr( $submitted_value )
				: esc_attr( $default_value )
		);
	}

	/**
	 * Generate the placeholder="" attribute.
	 *
	 * @return string
	 */
	protected function getControlPlaceholder() {
		return sprintf(
			'placeholder="%s"',
			esc_attr( $this->getBlockAttribute( 'fieldPlaceholder' ) )
		);
	}

	/**
	 * Apply the "checked" attribute if the control is selected.
	 *
	 * @return string
	 */
	protected function getControlSelected() {
		if ( ! $this->isOptionInput() ) {
			return '';
		}

		$submitted_value = $this->injestion->formValue( $this->field_name, $this->getBlockContext( 'omniform/fieldGroupName' ) );

		$is_selected = 'radio' === $this->getBlockAttribute( 'fieldType' )
			? $this->field_name === $submitted_value
			: $submitted_value;

		return empty( $is_selected ) ? '' : 'checked';
	}

	abstract function renderField();
}
