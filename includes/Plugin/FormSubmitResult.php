<?php
/**
 * Outcome of submitting a form through the domain path.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Response;
use OmniForm\Form\ValidationResult;

/**
 * Discriminated result of FormSubmitter::submit().
 */
final class FormSubmitResult {
	private function __construct(
		private readonly bool $success,
		private readonly ?Response $response = null,
		private readonly ?int $response_id = null,
		private readonly ?ValidationResult $validation = null,
		private readonly ?string $error_code = null,
		private readonly ?string $error_message = null,
	) {}

	/**
	 * Successful persist of a validated response.
	 */
	public static function success( Response $response, int $response_id ): self {
		return new self(
			success: true,
			response: $response,
			response_id: $response_id,
		);
	}

	/**
	 * Validation failed; nothing was saved.
	 */
	public static function validation_failed( ValidationResult $validation ): self {
		return new self(
			success: false,
			validation: $validation,
			error_code: 'validation_failed',
		);
	}

	/**
	 * Form missing, unpublished, or otherwise unavailable.
	 */
	public static function failed( string $code, string $message ): self {
		return new self(
			success: false,
			error_code: $code,
			error_message: $message,
		);
	}

	public function is_success(): bool {
		return $this->success;
	}

	public function is_validation_failure(): bool {
		return 'validation_failed' === $this->error_code;
	}

	public function response(): ?Response {
		return $this->response;
	}

	public function response_id(): ?int {
		return $this->response_id;
	}

	public function validation(): ?ValidationResult {
		return $this->validation;
	}

	public function error_code(): ?string {
		return $this->error_code;
	}

	public function error_message(): ?string {
		return $this->error_message;
	}

	/**
	 * Invalid field messages when validation failed.
	 *
	 * @return array<string, string>
	 */
	public function invalid_fields(): array {
		return $this->validation?->messages() ?? array();
	}
}
