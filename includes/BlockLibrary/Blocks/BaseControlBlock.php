<?php
/**
 * The BaseControlBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

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
		return $this->getBlockAttribute( 'isRequired' ) ?? $this->getBlockContext( 'omniform/fieldIsRequired' );
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
	 * Gets the control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		$parts = array_values(
			array_filter(
				array(
					$this->getFieldGroupName(),
					$this->getFieldName(),
				)
			)
		);

		return 2 === count( $parts )
			? sprintf( '%s[%s]', $parts[0], $parts[1] )
			: $parts[0];
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function renderControl();
}
