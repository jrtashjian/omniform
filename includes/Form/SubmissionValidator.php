<?php
/**
 * Submission validator port.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Validates a Submission against a FormSchema.
 *
 * Implementations live in the outer layer (e.g. Respect-based adapter).
 */
interface SubmissionValidator {
	/**
	 * Validate submission values against the schema.
	 *
	 * @param FormSchema $schema     Form field catalog.
	 * @param Submission $submission Submitted values.
	 */
	public function validate( FormSchema $schema, Submission $submission ): ValidationResult;
}
