<?php
/**
 * Tests for Captcha.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Captcha;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Captcha.
 */
class CaptchaTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Captcha
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'version' )->andReturn( '1.0.0' );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$this->block = new Captcha();
	}

	/**
	 * Data provider for render tests.
	 */
	public function render_data_provider() {
		return array(
			'hcaptcha'        => array(
				'attributes' => array(
					'service' => 'hcaptcha',
					'theme'   => 'light',
					'size'    => 'normal',
				),
				'expects'    => array( 'class="h-captcha"', 'data-service="hcaptcha"', 'data-sitekey="site_key_123"' ),
			),
			'recaptchav2'     => array(
				'attributes' => array( 'service' => 'recaptchav2' ),
				'expects'    => array( 'class="g-recaptcha"' ),
			),
			'recaptchav3'     => array(
				'attributes' => array( 'service' => 'recaptchav3' ),
				'expects'    => array( 'class="g-recaptcha"' ),
			),
			'turnstile'       => array(
				'attributes' => array( 'service' => 'turnstile' ),
				'expects'    => array( 'class="cf-turnstile"' ),
			),
			'no service'      => array(
				'attributes' => array(),
				'expects'    => '',
			),
			'invalid service' => array(
				'attributes' => array( 'service' => 'invalid' ),
				'expects'    => '',
			),
		);
	}

	/**
	 * Test render.
	 *
	 * @dataProvider render_data_provider
	 * @param array $attributes Block attributes.
	 * @param mixed $expects    Expected result.
	 */
	public function test_render( $attributes, $expects ) {
		\WP_Mock::userFunction( 'get_option' )->andReturn( 'site_key_123' );
		\WP_Mock::userFunction( 'wp_enqueue_script' )->andReturn( true );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $attrs ) {
				$attr_str = '';
				foreach ( $attrs as $key => $value ) {
					$attr_str .= ' ' . $key . '="' . $value . '"';
				}
				return trim( $attr_str );
			}
		);

		$result = $this->block->render_block( $attributes, '', $this->createBlockWithContext() );
		if ( is_array( $expects ) ) {
			foreach ( $expects as $expect ) {
				$this->assertStringContainsString( $expect, $result );
			}
		} else {
			$this->assertEquals( $expects, $result );
		}
	}

	/**
	 * Data provider for field label and name tests.
	 */
	public function field_data_provider() {
		return array(
			'hcaptcha'    => array(
				'service' => 'hcaptcha',
				'label'   => 'hCaptcha',
				'name'    => 'h-captcha-response',
			),
			'recaptchav2' => array(
				'service' => 'recaptchav2',
				'label'   => 'reCAPTCHA',
				'name'    => 'g-recaptcha-response',
			),
			'recaptchav3' => array(
				'service' => 'recaptchav3',
				'label'   => 'reCAPTCHA',
				'name'    => 'g-recaptcha-response',
			),
			'turnstile'   => array(
				'service' => 'turnstile',
				'label'   => 'Turnstile',
				'name'    => 'cf-turnstile-response',
			),
		);
	}

	/**
	 * Test get_field_label.
	 *
	 * @dataProvider field_data_provider
	 * @param string $service Service name.
	 * @param string $label   Expected label.
	 * @param string $name    Expected name.
	 */
	public function test_get_field_label( $service, $label, $name ) {
		$this->block->render_block( array( 'service' => $service ), '', $this->createBlockWithContext() );
		$this->assertEquals( $label, $this->block->get_field_label() );
	}

	/**
	 * Test get_field_name.
	 *
	 * @dataProvider field_data_provider
	 * @param string $service Service name.
	 * @param string $label   Expected label.
	 * @param string $name    Expected name.
	 */
	public function test_get_field_name( $service, $label, $name ) {
		$this->block->render_block( array( 'service' => $service ), '', $this->createBlockWithContext() );
		$this->assertEquals( $name, $this->block->get_field_name() );
	}

	/**
	 * Test get_field_group_name.
	 */
	public function test_get_field_group_name() {
		$this->assertNull( $this->block->get_field_group_name() );
	}

	/**
	 * Test get_validation_rules.
	 */
	public function test_get_validation_rules() {
		$this->block->render_block( array( 'service' => 'hcaptcha' ), '', \Mockery::mock( '\WP_Block' ) );
		$rules = $this->block->get_validation_rules();
		$this->assertCount( 2, $rules );
		$this->assertInstanceOf( \OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty::class, $rules[0] );
		$this->assertInstanceOf( \OmniForm\Validation\Rules\HCaptchaRule::class, $rules[1] );

		$this->block->render_block( array( 'service' => 'recaptchav2' ), '', \Mockery::mock( '\WP_Block' ) );
		$rules = $this->block->get_validation_rules();
		$this->assertInstanceOf( \OmniForm\Validation\Rules\ReCaptchaV2Rule::class, $rules[1] );

		$this->block->render_block( array( 'service' => 'recaptchav3' ), '', \Mockery::mock( '\WP_Block' ) );
		$rules = $this->block->get_validation_rules();
		$this->assertInstanceOf( \OmniForm\Validation\Rules\ReCaptchaV3Rule::class, $rules[1] );

		$this->block->render_block( array( 'service' => 'turnstile' ), '', \Mockery::mock( '\WP_Block' ) );
		$rules = $this->block->get_validation_rules();
		$this->assertInstanceOf( \OmniForm\Validation\Rules\TurnstileRule::class, $rules[1] );
	}

	/**
	 * Test render_control.
	 */
	public function test_render_control() {
		$this->assertEquals( '', $this->block->render_control() );
	}

	/**
	 * Test filter_request_params.
	 */
	public function test_filter_request_params() {
		$params = array( 'name', 'email' );
		$result = $this->block->filter_request_params( $params );
		$this->assertContains( 'g-recaptcha-response', $result );
		$this->assertContains( 'h-captcha-response', $result );
		$this->assertContains( 'cf-turnstile-response', $result );
	}
}
