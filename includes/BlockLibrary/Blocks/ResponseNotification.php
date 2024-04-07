<?php
/**
 * The ResponseNotification block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The ResponseNotification block class.
 */
class ResponseNotification extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
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

		return sprintf(
			'<div %s><p>%s</p></div>',
			get_block_wrapper_attributes(
				array(
					'class' => esc_attr( $this->get_block_attribute( 'messageType' ) . '-response-notification' ),
					'style' => 'display:none;',
				)
			),
			wp_kses( $this->get_block_attribute( 'messageContent' ), $allowed_html )
		);
	}
}
