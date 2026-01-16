<?php
/**
 * The UsernameOrEmailRule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Dependencies\Respect\Validation\Rules\AbstractRule;
use OmniForm\Dependencies\Respect\Validation\Validator;

/**
 * The UsernameOrEmailRule class.
 */
class UsernameOrEmailRule extends AbstractRule {
	/**
	 * Validates the input.
	 *
	 * @param mixed $input Input to validate.
	 *
	 * @return bool
	 */
	public function validate( $input ): bool {
		if ( Validator::email()->validate( $input ) ) {
			return true;
		}

		if ( validate_username( $input ) ) {
			return true;
		}

		return false;
	}
}
