<?php
/**
 * The Label block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Label block class.
 */
class Label extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->get_block_context( 'omniform/fieldLabel' ) ) ) {
			return '';
		}

		$extra_attributes = array_filter(
			array(
				'class' => $this->get_block_attribute( 'isHidden' ) ? 'screen-reader-text' : null,
			)
		);

		return sprintf(
			'<label for="%s" %s>%s</label>',
			esc_attr( $this->get_block_context( 'omniform/fieldName' ) ),
			get_block_wrapper_attributes( $extra_attributes ),
			wp_kses( $this->get_block_context( 'omniform/fieldLabel' ), $this->allowed_html_for_labels ) . $this->label_required()
		);
	}

	/**
	 * Returns the required label.
	 *
	 * @return string
	 */
	private function label_required() {
		if ( ! $this->get_block_context( 'omniform/fieldIsRequired' ) ) {
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
