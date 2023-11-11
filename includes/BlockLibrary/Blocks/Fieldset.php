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
		if ( empty( $this->get_block_attribute( 'fieldLabel' ) ) ) {
			return '';
		}

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

		if ( $this->get_block_attribute( 'isRequired' ) ) {
			$form_id = omniform()->get( Form::class )->get_id() ?? $this->get_block_context( 'postId' );

			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $allowed_html )
			);
		}

		return sprintf(
			'<fieldset %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			get_block_wrapper_attributes(),
			wp_kses( $this->get_block_attribute( 'fieldLabel' ), $allowed_html ) . $label_required,
			$this->content
		);
	}

	/**
	 * Gets the field group label.
	 *
	 * @return string|null
	 */
	public function get_field_group_label() {
		return $this->get_block_attribute( 'fieldLabel' );
	}

	/**
	 * Gets the field group name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_group_name() {
		return sanitize_title( $this->get_block_attribute( 'fieldName' ) ?? $this->get_field_group_label() ?? '' );
	}
}
