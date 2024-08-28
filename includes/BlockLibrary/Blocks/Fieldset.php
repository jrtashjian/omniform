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

		$label_required = null;

		if ( $this->get_block_attribute( 'isRequired' ) ) {
			$form_id = omniform()->get( Form::class )->get_id() ?? $this->get_block_context( 'postId' );

			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $this->allowed_html_for_labels )
			);
		}

		return sprintf(
			'<fieldset %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			get_block_wrapper_attributes(),
			wp_kses( $this->get_block_attribute( 'fieldLabel' ), $this->allowed_html_for_labels ) . $label_required,
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
		return sanitize_html_class( $this->get_block_attribute( 'fieldName' ) ?? $this->get_field_group_label() ?? '' );
	}
}
