<?php
/**
 * The BaseFieldBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The BaseFieldBlock block class.
 */
abstract class BaseFieldBlock extends BaseBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

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
	 * Get the sanitized omniform/fieldGroup key.
	 *
	 * @return string
	 */
	public function getFieldGroupName() {
		return $this->isGrouped()
			? sanitize_title( $this->getBlockContext( 'omniform/fieldGroupName' ) )
			: null;
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
		);
	}

	/**
	 * If the fieldGroupName context exists, the field is part of a group.
	 *
	 * @return bool
	 */
	public function isGrouped() {
		return ! empty( $this->getBlockContext( 'omniform/fieldGroupName' ) );
	}

	/**
	 * Does the field require a value?
	 *
	 * @return bool
	 */
	public function isRequired() {
		return ! empty( $this->getBlockAttribute( 'isRequired' ) );
	}

	/**
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderLabel() {
		$form_id = $this->getBlockContext( 'postId' );

		$allowed_html = array(
			'strong' => array(),
			'em'     => array(),
			'img'    => array(
				'class' => true,
				'style' => true,
				'src'   => true,
				'alt'   => true,
			),
		);

		$label_required = null;

		if ( $this->isRequired() && (
			'radio' !== $this->getBlockAttribute( 'fieldType' ) ||
			( 'radio' === $this->getBlockAttribute( 'fieldType' ) && ! $this->isGrouped() )
		) ) {
			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $allowed_html )
			);
		}

		$classnames = array_filter(
			array(
				'omniform-field-label',
				! empty( $this->getBlockAttribute( 'isLabelHidden' ) ) ? 'screen-reader-text' : null,
			)
		);

		return sprintf(
			'<label class="%s" for="%s">%s</label>',
			esc_attr( implode( ' ', $classnames ) ),
			esc_attr( $this->getFieldName() ),
			wp_kses( $this->getBlockAttribute( 'fieldLabel' ), $allowed_html ) . $label_required
		);
	}

	/**
	 * Render the input's error text element.
	 *
	 * @return string
	 */
	protected function renderFieldError() {
		$errors = false;
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
				$this->isRequired() ? 'required' : '',
			)
		);
	}

	public function getControlNameParts() {
		return $this->isGrouped()
			? array( $this->getFieldGroupName(), $this->getFieldName() )
			: array( $this->getFieldName() );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		$parts = $this->getControlNameParts();

		return 2 === count( $parts )
			? sprintf( '%s[%s]', $parts[0], $parts[1] )
			: $parts[0];
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function getControlValue() {
		return $this->getBlockAttribute( 'fieldValue' );
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function renderControl();
}
