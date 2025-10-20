<?php
/**
 * Tests for BaseBlock.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseBlock;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests for BaseBlock.
 */
class BaseBlockTest extends BaseTestCase {

	/**
	 * Test block instance.
	 *
	 * @var BaseBlock
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new TestBaseBlock();
	}

	/**
	 * Test block_type_name.
	 */
	public function test_block_type_name() {
		$reflection = new \ReflectionMethod( $this->block, 'block_type_name' );
		$reflection->setAccessible( true );
		$this->assertEquals( 'test-base-block', $reflection->invoke( $this->block ) );
	}

	/**
	 * Test block_type_metadata.
	 */
	public function test_block_type_metadata() {
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'base_path' )->andReturnUsing(
			function ( $path ) {
				return '/base' . $path;
			}
		);
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$this->assertEquals( '/base/build/block-library/test-base-block', $this->block->block_type_metadata() );
	}

	/**
	 * Test render_block.
	 */
	public function test_render_block() {
		$attributes = array( 'key' => 'value' );
		$content    = 'inner content';
		$block      = \Mockery::mock( '\WP_Block' );

		$result = $this->block->render_block( $attributes, $content, $block );

		$this->assertEquals( 'rendered content', $result );
		$this->assertEquals( 'value', $this->block->get_block_attribute( 'key' ) );
	}

	/**
	 * Test get_block_attribute.
	 */
	public function test_get_block_attribute() {
		$this->block->render_block( array( 'existing' => 'value' ), '', \Mockery::mock( '\WP_Block' ) );

		$this->assertEquals( 'value', $this->block->get_block_attribute( 'existing' ) );
		$this->assertNull( $this->block->get_block_attribute( 'nonexistent' ) );
	}

	/**
	 * Test get_block_context.
	 */
	public function test_get_block_context() {
		$block_with_context          = \Mockery::mock( '\WP_Block' );
		$block_with_context->context = array( 'ctx_key' => 'ctx_value' );

		$this->block->render_block( array(), '', $block_with_context );

		$this->assertEquals( 'ctx_value', $this->block->get_block_context( 'ctx_key' ) );
		$this->assertNull( $this->block->get_block_context( 'nonexistent' ) );

		// Test without context property.
		$block_without_context = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block_without_context );

		$this->assertNull( $this->block->get_block_context( 'any' ) );
	}
}

// phpcs:disable

/**
 * Concrete implementation for testing BaseBlock.
 */
class TestBaseBlock extends BaseBlock {
	protected function render() {
		return 'rendered content';
	}
}
