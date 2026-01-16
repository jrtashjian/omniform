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
					'class' => implode(
						' ',
						array(
							esc_attr( $this->get_message_type() . '-response-notification' ),
							esc_attr( 'is-style-' . $this->get_message_type() ),
						)
					),
					'style' => $this->notification_display( $form ),
				)
			),
			wp_kses( $this->get_block_attribute( 'messageContent' ), $this->allowed_html_for_labels ),
			do_blocks( $this->content )
		);
	}

	/**
	 * Gets the message type from the block class name.
	 *
	 * @return string The message type.
	 */
	private function get_message_type() {
		$classname = $this->get_block_attribute( 'className' ) ?? '';

		if ( preg_match( '/is-style-([^\s]+)/', $classname, $matches ) ) {
			return $matches[1];
		}

		// Fallback for success messageType pre-'is-style-'.
		$has_success_border = isset( $this->attributes['style']['border']['left']['color'] )
			&& 'var(--wp--preset--color--vivid-green-cyan,#00d084)' === $this->attributes['style']['border']['left']['color'];
		if ( $this->get_block_attribute( 'messageType' ) === 'success' || $has_success_border ) {
			return 'success';
		}

		// Fallback for error messageType pre-'is-style-'.
		$has_error_border = isset( $this->attributes['style']['border']['left']['color'] )
			&& 'var(--wp--preset--color--vivid-red,#cf2e2e)' === $this->attributes['style']['border']['left']['color'];
		if ( $this->get_block_attribute( 'messageType' ) === 'error' || $has_error_border ) {
			return 'error';
		}

		return 'info';
	}

	/**
	 * Determines whether the notification should be displayed.
	 *
	 * @param \OmniForm\Plugin\Form $form The form object.
	 *
	 * @return string The display style.
	 */
	private function notification_display( \OmniForm\Plugin\Form $form ) {
		$message_type = $this->get_message_type();

		if (
			( 'error' === $message_type && $form->validation_failed() ) ||
			( 'success' === $message_type && $form->validation_succeeded() )
		) {
			return 'display:block;';
		}

		return in_array( $message_type, array( 'success', 'error' ), true )
			? 'display:none;'
			: '';
	}
}
