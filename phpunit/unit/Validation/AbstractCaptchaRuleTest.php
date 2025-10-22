<?php
/**
 * Abstract base test case for CAPTCHA rule tests.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use WP_Mock;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Abstract base test case for CAPTCHA rule tests.
 */
abstract class AbstractCaptchaRuleTest extends BaseTestCase {
	/**
	 * Get the option key for the secret key.
	 *
	 * @return string
	 */
	abstract protected function getOptionKey(): string;

	/**
	 * Get the verification URL.
	 *
	 * @return string
	 */
	abstract protected function getVerifyUrl(): string;

	/**
	 * Get the rule class name.
	 *
	 * @return string
	 */
	abstract protected function getRuleClass(): string;

	/**
	 * Test validate method returns true when no secret key is set.
	 */
	public function testValidateReturnsTrueWhenNoSecretKey() {
		WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( $this->getOptionKey() ),
				'return' => false,
			)
		);

		$rule_class = $this->getRuleClass();
		$rule       = new $rule_class();
		$this->assertTrue( $rule->validate( 'test_response' ) );
	}

	/**
	 * Test validate method returns false when wp_remote_post fails.
	 */
	public function testValidateReturnsFalseWhenRemotePostFails() {
		WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( $this->getOptionKey() ),
				'return' => 'test_secret',
			)
		);

		$mock_error = (object) array( 'errors' => array( 'http_request_failed' => array( 'Request failed' ) ) );

		WP_Mock::userFunction(
			'wp_remote_post',
			array(
				'args'   => array(
					$this->getVerifyUrl(),
					array(
						'body' => array(
							'secret'   => 'test_secret',
							'response' => 'test_response',
						),
					),
				),
				'return' => $mock_error,
			)
		);

		WP_Mock::userFunction(
			'is_wp_error',
			array(
				'args'   => array( $mock_error ),
				'return' => true,
			)
		);

		$rule_class = $this->getRuleClass();
		$rule       = new $rule_class();
		$this->assertFalse( $rule->validate( 'test_response' ) );
	}

	/**
	 * Test validate method returns true when CAPTCHA validation succeeds.
	 */
	public function testValidateReturnsTrueWhenValidationSucceeds() {
		WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( $this->getOptionKey() ),
				'return' => 'test_secret',
			)
		);

		WP_Mock::userFunction(
			'wp_remote_post',
			array(
				'args'   => array(
					$this->getVerifyUrl(),
					array(
						'body' => array(
							'secret'   => 'test_secret',
							'response' => 'test_response',
						),
					),
				),
				'return' => array( 'body' => '{"success": true}' ),
			)
		);

		WP_Mock::userFunction(
			'is_wp_error',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'array' ) ),
				'return' => false,
			)
		);

		WP_Mock::userFunction(
			'wp_remote_retrieve_body',
			array(
				'args'   => array( array( 'body' => '{"success": true}' ) ),
				'return' => '{"success": true}',
			)
		);

		$rule_class = $this->getRuleClass();
		$rule       = new $rule_class();
		$this->assertTrue( $rule->validate( 'test_response' ) );
	}

	/**
	 * Test validate method returns false when CAPTCHA validation fails.
	 */
	public function testValidateReturnsFalseWhenValidationFails() {
		WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( $this->getOptionKey() ),
				'return' => 'test_secret',
			)
		);

		WP_Mock::userFunction(
			'wp_remote_post',
			array(
				'args'   => array(
					$this->getVerifyUrl(),
					array(
						'body' => array(
							'secret'   => 'test_secret',
							'response' => 'test_response',
						),
					),
				),
				'return' => array( 'body' => '{"success": false}' ),
			)
		);

		WP_Mock::userFunction(
			'is_wp_error',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'array' ) ),
				'return' => false,
			)
		);

		WP_Mock::userFunction(
			'wp_remote_retrieve_body',
			array(
				'args'   => array( array( 'body' => '{"success": false}' ) ),
				'return' => '{"success": false}',
			)
		);

		$rule_class = $this->getRuleClass();
		$rule       = new $rule_class();
		$this->assertFalse( $rule->validate( 'test_response' ) );
	}
}
