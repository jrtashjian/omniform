<?php
/**
 * Tests the Button block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use Mockery;
use OmniForm\BlockLibrary\Blocks\Button;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the Button block.
 */
class ButtonTest extends BaseTestCase {

	/**
	 * The Button instance.
	 *
	 * @var Button
	 */
	private $block;

	/**
	 * The WP_Block mock instance.
	 *
	 * @var \WP_Block|Mockery\MockInterface
	 */
	private $wp_block_mock;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Button();

		$this->wp_block_mock = $this->createMock( \stdClass::class );

		WP_Mock::userFunction( 'wp_theme_get_element_class_name' )->with( 'button' )->andReturn( 'wp-element-button' );

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );
	}

	/**
	 * Test render_block returns button with default type.
	 */
	public function testRenderBlockDefault() {
		$result = $this->block->render_block(
			array( 'buttonLabel' => 'Submit' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<button class="wp-element-button" type="button">Submit</button>', $result );
	}

	/**
	 * Test render_block returns button with submit type.
	 */
	public function testRenderBlockSubmit() {
		$result = $this->block->render_block(
			array(
				'buttonLabel' => 'Submit',
				'buttonType'  => 'submit',
			),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<button class="wp-element-button" type="submit">Submit</button>', $result );
	}

	/**
	 * Test render_block returns button with reset type.
	 */
	public function testRenderBlockReset() {
		$result = $this->block->render_block(
			array(
				'buttonLabel' => 'Reset',
				'buttonType'  => 'reset',
			),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<button class="wp-element-button" type="reset">Reset</button>', $result );
	}

	/**
	 * Test render_block returns empty string without label.
	 */
	public function testRenderBlockWithoutLabel() {
		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '', $result );
	}
}
