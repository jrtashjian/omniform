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

		return sprintf(
			'<fieldset %1$s><legend>%2$s</legend><div class="omniform-field-label" aria-hidden="true">%2$s</div>%3$s</fieldset>',
			get_block_wrapper_attributes(),
			wp_kses( $this->get_block_attribute( 'fieldLabel' ), $this->allowed_html_for_labels ) . $this->label_required(),
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

	/**
	 * Returns the required label.
	 *
	 * @return string
	 */
	private function label_required() {
		if ( ! $this->get_block_attribute( 'isRequired' ) ) {
			return '';
		}

		$required_label = omniform()->get( \OmniForm\Plugin\Form::class )->get_required_label();

		return ( '*' === $required_label )
			? sprintf(
				'<abbr class="omniform-field-required" title="%s">*</abbr>',
				esc_attr__( 'required', 'omniform' )
			)
			: sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( $required_label, $this->allowed_html_for_labels )
			);
	}
}
