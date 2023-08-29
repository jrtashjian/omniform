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

		if ( $this->get_block_context( 'omniform/fieldIsRequired' ) ) {
			$form_id = omniform()->get( \OmniForm\Plugin\Form::class )->get_id() ?? $this->get_block_context( 'postId' );

			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $allowed_html )
			);
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
			wp_kses( $this->get_block_context( 'omniform/fieldLabel' ), $allowed_html ) . $label_required
		);
	}
}
