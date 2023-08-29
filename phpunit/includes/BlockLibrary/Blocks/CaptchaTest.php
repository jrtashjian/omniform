<?php
/**
 * Tests the Captcha class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Captcha;

/**
 * Tests the Captcha class.
 */
class CaptchaTest extends FormBlockTestCase {
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new CaptchaBlock() );
	}

	/**
	 * Make sure the block does not render markup if the service attribute is empty.
	 */
	public function test_does_not_render_without_service() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->assertNotEmpty(
			$this->render_block_with_attributes(
				array(
					'service' => 'hcaptcha',
				)
			)
		);

		$this->assertEmpty(
			$this->render_block_with_attributes(
				array(
					'service' => 'none-existant-service',
				)
			)
		);
	}
}

// phpcs:disable
class CaptchaBlock extends Captcha {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}