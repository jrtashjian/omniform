<?php
/**
 * Tests the SelectOption block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use Mockery;
use OmniForm\BlockLibrary\Blocks\SelectOption;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the SelectOption block.
 */
class SelectOptionTest extends BaseTestCase {

	/**
	 * The SelectOption instance.
	 *
	 * @var SelectOption
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

		$this->block = new SelectOption();

		$this->wp_block_mock          = $this->createMock( \stdClass::class );
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Option' );

		WP_Mock::userFunction( 'sanitize_html_class' )->andReturnArg( 0 );
		WP_Mock::userFunction( 'wp_strip_all_tags' )->andReturnArg( 0 );

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );
	}

	/**
	 * Test render_block returns option with label.
	 */
	public function testRenderBlockWithLabel() {
		$result = $this->block->render_block(
			array( 'fieldLabel' => 'Test Option' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<option value="Test Option">Test Option</option>', $result );
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
