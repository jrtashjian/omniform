<?php
/**
 * Tests the SelectGroup block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use Mockery;
use OmniForm\BlockLibrary\Blocks\SelectGroup;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the SelectGroup block.
 */
class SelectGroupTest extends BaseTestCase {

	/**
	 * The SelectGroup instance.
	 *
	 * @var SelectGroup
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

		$this->block = new SelectGroup();

		$this->wp_block_mock          = $this->createMock( \stdClass::class );
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Group' );

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
	 * Test render_block returns optgroup with label and content.
	 */
	public function testRenderBlockWithLabel() {
		$content = '<option value="option1">Option 1</option><option value="option2">Option 2</option>';

		$result = $this->block->render_block(
			array( 'fieldLabel' => 'Test Group' ),
			$content,
			$this->wp_block_mock
		);

		$this->assertEquals( '<optgroup label="Test Group">' . $content . '</optgroup>', $result );
	}

	/**
	 * Test render_block returns empty string without label.
	 */
	public function testRenderBlockWithoutLabel() {
		$result = $this->block->render_block(
			array(),
			'<option value="option1">Option 1</option>',
			$this->wp_block_mock
		);

		$this->assertEquals( '', $result );
	}
}
