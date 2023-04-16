<?php
/**
 * The BaseControlBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;

/**
 * The BaseControlBlock block class.
 */
abstract class BaseControlBlock extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		return $this->getFieldLabel() ? $this->renderControl() : '';
	}

	/**
	 * Gets the field label.
	 *
	 * @return string|null
	 */
	public function getFieldLabel() {
		return $this->getBlockContext( 'omniform/fieldLabel' );
	}

	/**
	 * Gets the field name (sanitized).
	 *
	 * @return string|null
	 */
	public function getFieldName() {
		return sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ?? $this->getFieldLabel() );
	}

	/**
	 * Gets the field group label.
	 *
	 * @return string|null
	 */
	public function getFieldGroupLabel() {
		return $this->getBlockContext( 'omniform/fieldGroupLabel' );
	}

	/**
	 * Gets the field group name (sanitized).
	 *
	 * @return string|null
	 */
	public function getFieldGroupName() {
		return sanitize_title( $this->getBlockContext( 'omniform/fieldGroupName' ) ?? $this->getFieldGroupLabel() );
	}

	/**
	 * Is the field grouped?
	 *
	 * @return bool
	 */
	public function isGrouped() {
		return ! empty( $this->getFieldGroupName() );
	}

	/**
	 * Is the field required?
	 *
	 * @return bool
	 */
	public function isRequired() {
		return $this->getBlockContext( 'omniform/fieldGroupIsRequired' ) ?? $this->getBlockContext( 'omniform/fieldIsRequired' );
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function getExtraWrapperAttributes() {
		return array_filter(
			array(
				'id'       => $this->getFieldName(),
				'name'     => $this->getControlName(),
				'required' => $this->isRequired(),
			)
		);
	}

	/**
	 * Gets the control's name parts.
	 *
	 * @return array
	 */
	public function getControlNameParts() {
		return array_values(
			array_filter(
				array(
					$this->getFieldGroupName(),
					$this->getFieldName(),
				)
			)
		);
	}

	/**
	 * Gets the control's name attribute.
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
	 * Get the validation rules for the field.
	 *
	 * @return array
	 */
	public function getValidationRules() {
		return array_filter(
			array(
				$this->isRequired() ? new Validation\Rules\NotEmpty() : null,
			)
		);
	}

	/**
	 * Does the field have validation rules?
	 *
	 * @return bool
	 */
	public function hasValidationRules() {
		return ! empty( $this->getValidationRules() );
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function renderControl();
}
