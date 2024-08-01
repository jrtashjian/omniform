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

		// Render validation messages if they exist.
		if ( $form->get_validation_messages() ) {
			$this->content = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";

			foreach ( $form->get_validation_messages() as $message ) {
				$this->content .= sprintf(
					"<!-- wp:list-item -->\n<li>%s</li>\n<!-- /wp:list-item -->",
					esc_html( $message ),
				);
			}

			$this->content .= "</ul>\n<!-- /wp:list -->";
		}

		return sprintf(
			'<div %s><p>%s</p>%s</div>',
			get_block_wrapper_attributes(
				array(
					'class' => esc_attr( $this->get_block_attribute( 'messageType' ) . '-response-notification' ),
					'style' => $this->notification_display( $form ),
				)
			),
			wp_kses( $this->get_block_attribute( 'messageContent' ), $allowed_html ),
			do_blocks( $this->content )
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
