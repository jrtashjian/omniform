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

		$form = omniform()->get( \OmniForm\Plugin\Form::class );

		return sprintf(
			'<div %s><p>%s</p>%s</div>',
			get_block_wrapper_attributes(
				array(
					'class' => esc_attr( $this->get_block_attribute( 'messageType' ) . '-response-notification' ),
					'style' => $this->notification_display( $form ),
				)
			),
			wp_kses( $this->get_block_attribute( 'messageContent' ), $allowed_html ),
			$this->content
		);
	}

	/**
	 * Determines whether the notification should be displayed.
	 *
	 * @param \OmniForm\Plugin\Form $form The form object.
	 *
	 * @return string The display style.
	 */
	private function notification_display( \OmniForm\Plugin\Form $form ) {
		$message_type = $this->get_block_attribute( 'messageType' );

		if (
			( 'error' === $message_type && $form->validation_failed() ) ||
			( 'success' === $message_type && $form->validation_succeeded() )
		) {
			return 'display:block;';
		}

		return 'display:none;';
	}
}
