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
class SelectOption extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		$name = empty( $this->getBlockAttribute( 'fieldName' ) )
			? sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) )
			: $this->getBlockAttribute( 'fieldName' );

		$form_ingestion = omniform()->get( FormIngestionEngine::class );

		$target = $form_ingestion->formValue(
			array(
				$this->getBlockContext( 'omniform/fieldGroupName' ),
				$this->getBlockContext( 'omniform/fieldSelectName' ),
			)
		);

		$is_selected = is_array( $target )
			? in_array( $name, $target )
			: $name === $target;

		return sprintf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $name ),
			$is_selected ? ' selected ' : '',
			esc_attr( $this->getBlockAttribute( 'fieldLabel' ) )
		);
	}
}
