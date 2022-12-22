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

		$attributes = get_block_wrapper_attributes(
			array( 'class' => implode( ' ', $this->getDefaultClasses() ) )
		);

		return sprintf(
			'<div %s>%s</div>',
			$attributes,
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
			? sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) )
			: sanitize_title( $field_name );
	}

	/**
	 * Get the default classes to be applied to the block wrapper.
	 *
	 * @return array
	 */
	public function getDefaultClasses() {
		return array(
			// Apply custom class for each field type.
			empty( $this->getBlockAttribute( 'fieldType' ) )
				? 'omniform-' . $this->blockTypeName()
				: 'omniform-field-' . $this->getBlockAttribute( 'fieldType' ),

			$this->getBlockAttribute( 'isRequired' )
				? 'field-required'
				: '',
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
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderLabel() {
		$form_id = $this->injestion->getFormId() ?? $this->getBlockContext( 'postId' );

		$label_required = ! $this->getBlockAttribute( 'isRequired' )
			? null
			: sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses_post( get_post_meta( $form_id, 'required_label', true ) )
			);

		return sprintf(
			'<label class="omniform-field-label" for="%s">%s</label>',
			esc_attr( $this->getFieldName() ),
			wp_kses_post( $this->getBlockAttribute( 'fieldLabel' ) ) . $label_required
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
	 * @return array
	 */
	protected function getControlAttributes() {
		return array_filter(
			array(
				$this->getElementAttribute( 'id', sanitize_title( $this->getFieldName() ) ),
				$this->getElementAttribute( 'name', $this->getControlName() ),
			)
		);
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
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	protected function getControlValue() {
		$submitted_value = $this->injestion->formValue(
			array(
				$this->getBlockContext( 'omniform/fieldGroupName' ),
				$this->getFieldName(),
			)
		);

		return empty( $submitted_value )
			? $this->getBlockAttribute( 'fieldValue' )
			: $submitted_value;
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function renderControl();
}
