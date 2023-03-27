<?php
/**
 * The Fieldset block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\Form;

/**
 * The Fieldset block class.
 */
class Fieldset extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

		$form_id = omniform()->get( Form::class )->getId() ?? $this->getBlockContext( 'postId' );

		$allowed_html = array(
			'strong' => array(),
			'em'     => array(),
		);

		$label_required = null;

		if ( $this->getBlockAttribute( 'isRequired' ) ) {
			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $allowed_html )
			);
		}

		return sprintf(
			'<fieldset %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			get_block_wrapper_attributes(),
			wp_kses( $this->getBlockAttribute( 'fieldLabel' ), $allowed_html ) . $label_required,
			$this->content
		);
	}
}
