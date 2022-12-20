<?php
/**
 * The SelectOption block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\FormIngestionEngine;

/**
 * The SelectOption block class.
 */
class SelectOption extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		$this->injestion = omniform()->get( FormIngestionEngine::class );

		return empty( $this->getBlockAttribute( 'fieldLabel' ) )
			? ''
			: $this->renderControl();
	}

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderControl() {
		$target = $this->injestion->formValue(
			array(
				$this->getBlockContext( 'omniform/fieldGroupName' ),
				$this->getBlockContext( 'omniform/fieldSelectName' ),
			)
		);

		$is_selected = is_array( $target )
			? in_array( $this->getFieldName(), $target, true )
			: $this->getFieldName() === $target;

		$attributes = array_filter(
			array(
				$this->getElementAttribute( 'value', $this->getFieldName() ),
				$is_selected ? 'selected' : null,
			)
		);

		return sprintf(
			'<option %s>%s</option>',
			trim( implode( ' ', $attributes ) ),
			esc_attr( $this->getBlockAttribute( 'fieldLabel' ) )
		);
	}
}
