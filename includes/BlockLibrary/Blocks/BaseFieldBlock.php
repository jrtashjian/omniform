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
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

		$this->injestion = omniform()->get( FormIngestionEngine::class );

		$attributes = array(
			$this->getElementAttribute( 'class', $this->getDefaultClasses() ),
			$this->getElementAttribute( 'style', $this->getColorStyles( $this->attributes ) ),
		);

		return sprintf(
			'<div %s>%s</div>',
			implode( ' ', $attributes ),
			$this->renderLabel() . $this->renderControl()
		);
	}

	/**
	 * Get the sanitized fieldName key. Fallback to fieldLabel.
	 *
	 * @return string
	 */
	public function getFieldName() {
		$field_name = $this->getBlockAttribute( 'fieldName' );
		return empty( $field_name )
			? sanitize_key( $this->getBlockAttribute( 'fieldLabel' ) )
			: sanitize_key( $field_name );
	}

	/**
	 * Get the default classes to be applied to the block wrapper.
	 *
	 * @return array
	 */
	public function getDefaultClasses() {
		$default = array(
			$this->blockTypeClassname(),

			// Apply custom class for each field type.
			empty( $this->getBlockAttribute( 'fieldType' ) )
				? 'omniform-' . $this->blockTypeName()
				: 'omniform-field-' . $this->getBlockAttribute( 'fieldType' ),

			$this->getBlockAttribute( 'isRequired' )
				? 'field-required'
				: '',
		);

		return array_merge(
			$default,
			$this->getColorClasses( $this->attributes ),
		);
	}

	/**
	 * If the fieldGroupName context exists, the field is part of a group.
	 *
	 * @return bool
	 */
	protected function isGrouped() {
		return ! empty( $this->getBlockContext( 'omniform/fieldGroupName' ) );
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
	protected function renderLabel() {
		return sprintf(
			'<label class="omniform-field-label" for="%s">%s</label>',
			esc_attr( $this->getFieldName() ),
			wp_kses_post( $this->getBlockAttribute( 'fieldLabel' ) )
		);
	}

	/**
	 * Render the input's error text element.
	 *
	 * @return string
	 */
	protected function renderFieldError() {
		$errors = $this->injestion->fieldError( $this->getFieldName() );
		return empty( $errors ) ? '' : sprintf(
			'<p class="omniform-field-support" style="color:red;">%s</p>',
			wp_kses_post( $errors )
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
					$this->getElementAttribute( 'name', $this->getControlName() ),
					$this->getControlPlaceholder(),
					$this->getControlValue(),
					$this->getControlSelected(),
					$this->getControlMultiple(),
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
		return $this->getElementAttribute( 'id', sanitize_title( $this->getFieldName() ) );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	protected function getControlName() {
		return $this->isGrouped()
			? $this->getBlockContext( 'omniform/fieldGroupName' ) . '[' . $this->getFieldName() . ']'
			: $this->getFieldName();
	}

	/**
	 * Generate the value="" attribute.
	 *
	 * @return string
	 */
	protected function getControlValue() {
		if ( 'field-select' === $this->blockTypeName() ) {
			return '';
		}

		$default_value = $this->isOptionInput()
			? $this->getFieldName()
			: $this->getBlockAttribute( 'fieldValue' );

		if ( 'checkbox' === $this->getBlockAttribute( 'fieldType' ) ) {
			$default_value = true;
		}

		$submitted_value = 'radio' === $this->getBlockAttribute( 'fieldType' )
			? $default_value
			: $this->injestion->formValue(
				array(
					$this->getBlockContext( 'omniform/fieldGroupName' ),
					$this->getFieldName(),
				)
			);

		return $this->getElementAttribute(
			'value',
			$submitted_value && ! $this->isHiddenInput()
				? $submitted_value
				: $default_value
		);
	}

	/**
	 * Generate the placeholder="" attribute.
	 *
	 * @return string
	 */
	protected function getControlPlaceholder() {
		return $this->getElementAttribute( 'placeholder', $this->getBlockAttribute( 'fieldPlaceholder' ) );
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

		$submitted_value = $this->injestion->formValue(
			array(
				$this->getBlockContext( 'omniform/fieldGroupName' ),
				'radio' === $this->getBlockAttribute( 'fieldType' ) ? '' : $this->getFieldName(),
			)
		);

		$is_selected = 'radio' === $this->getBlockAttribute( 'fieldType' )
			? $this->getFieldName() === $submitted_value
			: $submitted_value;

		return empty( $is_selected ) ? '' : 'checked';
	}

	protected function getControlMultiple() {
		return $this->getBlockAttribute( 'isMultiple' ) ? 'multiple' : '';
	}

	abstract function renderControl();
}
