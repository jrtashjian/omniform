<?php
/**
 * The FieldSelect block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The FieldSelect block class.
 */
class FieldSelect extends BaseFieldBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderControl() {
		$placeholder_option = '';
		if ( ! empty( $this->attributes['fieldPlaceholder'] ) ) {
			$placeholder_option = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->attributes['fieldPlaceholder'] )
			);
		}

		return sprintf(
			'<select class="omniform-field-control" %s>%s</select>',
			$this->getControlAttributes(),
			$placeholder_option . $this->content
		);
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	protected function getControlName() {
		$name = parent::getControlName();

		return $this->getBlockAttribute( 'isMultiple' )
			? $name . '[]'
			: $name;
	}
}
