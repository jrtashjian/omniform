<?php
/**
 * Request-scoped form render context.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * Holds render-time values shared with child blocks during form output.
 *
 * Set by the Form block before do_blocks(); Label/Fieldset read required_label().
 * Registered as shared in the container for the request lifetime.
 */
class FormRenderContext {
	/**
	 * Required-field indicator for the form currently being rendered.
	 */
	private string $required_label = '*';

	/**
	 * Set the required-field indicator for the current form render.
	 *
	 * @param string $required_label Indicator text (e.g. * or (required)).
	 */
	public function set_required_label( string $required_label ): void {
		$this->required_label = '' !== $required_label ? $required_label : '*';
	}

	/**
	 * Required-field indicator for the form currently being rendered.
	 */
	public function required_label(): string {
		return $this->required_label;
	}
}
