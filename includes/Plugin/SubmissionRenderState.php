<?php
/**
 * Request-scoped submission validation state for PHP postback rendering.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * Tracks validation outcome so ResponseNotification can show success/error on postback.
 *
 * Standard REST forms leave this empty (JS toggles visibility). Standalone forms
 * set it after domain validation. Registered as shared for the request.
 */
class SubmissionRenderState {
	/**
	 * Validation outcome: null = not evaluated this request.
	 */
	private ?bool $validation_passed = null;

	/**
	 * Validation messages keyed by field path.
	 *
	 * @var array<string, string>
	 */
	private array $messages = array();

	/**
	 * Mark validation as succeeded for this request.
	 */
	public function mark_succeeded(): void {
		$this->validation_passed = true;
		$this->messages          = array();
	}

	/**
	 * Mark validation as failed with messages.
	 *
	 * @param array<string, string> $messages Field path key => message.
	 */
	public function mark_failed( array $messages ): void {
		$this->validation_passed = false;
		$this->messages          = $messages;
	}

	/**
	 * Whether validation failed this request.
	 */
	public function validation_failed(): bool {
		return false === $this->validation_passed;
	}

	/**
	 * Whether validation succeeded this request.
	 */
	public function validation_succeeded(): bool {
		return true === $this->validation_passed;
	}

	/**
	 * Validation messages (empty when not failed).
	 *
	 * @return array<string, string>
	 */
	public function messages(): array {
		return $this->messages;
	}
}
