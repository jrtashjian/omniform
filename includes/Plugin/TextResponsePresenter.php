<?php
/**
 * Plain-text presentation of a form response.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Response;

/**
 * Renders a Response as plain text suitable for email bodies.
 */
final class TextResponsePresenter {
	public function __construct(
		private readonly FieldValueFormatter $formatter = new FieldValueFormatter(),
	) {}

	/**
	 * Present schema fields in order with submitted values.
	 *
	 * @param Response     $response     Domain response.
	 * @param list<string> $footer_lines Optional lines appended after a separator.
	 */
	public function present( Response $response, array $footer_lines = array() ): string {
		$lines = array();

		foreach ( $response->schema()->fields() as $field ) {
			$value = $this->formatter->format(
				$field,
				$response->submission()->value( $field->name() )
			);

			$lines[] = $field->label() . ': ' . $value;
		}

		if ( array() !== $footer_lines ) {
			$lines[] = '';
			$lines[] = '---';
			foreach ( $footer_lines as $line ) {
				if ( is_string( $line ) && '' !== $line ) {
					$lines[] = $line;
				}
			}
		}

		return implode( "\n", $lines );
	}
}
