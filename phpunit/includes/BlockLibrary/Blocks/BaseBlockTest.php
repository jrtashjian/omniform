<?php
/**
 * Tests the BaseBlock class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseBlock;

/**
 * Tests the BaseBlock class.
 */
class BaseBlockTest extends \WP_UnitTestCase {
	/**
	 * Ensure the path to the block type metadata is correct.
	 */
	public function test_block_type_metadata() {
		$this->assertEquals(
			'/build/block-library/base-block-stub',
			( new BaseBlockStub() )->block_type_metadata()
		);
	}

	/**
	 * Ensure tthe block renders correctly.
	 */
	public function test_render_block() {
		$this->assertEquals(
			'BaseBlockStub::render',
			( new BaseBlockStub() )->render_block( array(), '', (object) array() )
		);
	}

	/**
	 * Ensure a block attribute can be retrieved.
	 */
	public function test_get_block_attribute() {
		$attributes = array( 'one' => 'value' );

		$block_type = new BaseBlockStub();
		$block_type->render_block( $attributes, '', (object) array() );

		$this->assertEquals( $attributes['one'], $block_type->get_block_attribute( 'one' ) );
		$this->assertNull( $block_type->get_block_attribute( 'two' ) );
	}

	/**
	 * Ensure a block context can be retrieved.
	 */
	public function test_get_block_context() {
		$context = array( 'context' => array( 'one' => 'value' ) );

		$block_type = new BaseBlockStub();
		$block_type->render_block( array(), '', (object) $context );

		$this->assertEquals( $context['context']['one'], $block_type->get_block_context( 'one' ) );
		$this->assertNull( $block_type->get_block_context( 'two' ) );
	}
}

// phpcs:disable
/**
 * Stub class for testing the BaseBlock class.
 */
class BaseBlockStub extends BaseBlock {
	public function render() {
		return 'BaseBlockStub::render';
	}
}