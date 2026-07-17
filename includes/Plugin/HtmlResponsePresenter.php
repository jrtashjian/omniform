<?php
/**
 * HTML presentation of a form response.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Response;

/**
 * Renders a Response as HTML suitable for admin and rich notifications.
 */
final class HtmlResponsePresenter {
	public function __construct(
		private readonly FieldValueFormatter $formatter = new FieldValueFormatter(),
	) {}

	/**
	 * Present schema fields in order with submitted values.
	 */
	public function present( Response $response ): string {
		$lines = array();

		foreach ( $response->schema()->fields() as $field ) {
			$value = $this->formatter->format(
				$field,
				$response->submission()->value( $field->name() )
			);

			$lines[] = sprintf(
				'<strong>%s:</strong> %s',
				esc_html( $field->label() ),
				wp_kses_post( nl2br( esc_html( $value ) ) )
			);
		}

		return implode( '<br />', $lines );
	}
}
