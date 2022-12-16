<?php
/**
 * The FieldTextarea block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldTextarea block class.
 */
class FieldTextarea extends BaseFieldBlock {

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderField() {
		// if ( ! empty( $this->attributes['height'] ) ) {
		// 	$field_attributes['style'] = 'height: ' . $this->attributes['height'] . 'px;';
		// }

		return sprintf(
			'<textarea class="omniform-field-control" %s>%s</textarea>',
			$this->getControlAttributes(),
			esc_textarea( '' )
		);
	}
}
