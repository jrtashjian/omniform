<?php
/**
 * Validation result.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Outcome of validating a submission against a form schema.
 */
final class ValidationResult {
	/**
	 * Constructor.
	 *
	 * @param bool                  $valid    Whether validation succeeded.
	 * @param array<string, string> $messages Field path key => message.
	 */
	private function __construct(
		private readonly bool $valid,
		private readonly array $messages = array(),
	) {}

	/**
	 * Successful validation.
	 */
	public static function valid(): self {
		return new self( true );
	}

	/**
	 * Failed validation with messages.
	 *
	 * @param array<string, string> $messages Field path key => message.
	 */
	public static function invalid( array $messages ): self {
		return new self( false, $messages );
	}

	/**
	 * Whether validation succeeded.
	 */
	public function is_valid(): bool {
		return $this->valid;
	}

	/**
	 * Whether validation failed.
	 */
	public function is_invalid(): bool {
		return ! $this->valid;
	}

	/**
	 * Error messages keyed by field path.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array {
		return $this->messages;
	}
}
