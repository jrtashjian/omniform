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
	public function test_block_type_metadata() {
		$this->assertEquals(
			'/build/block-library/base-block-stub',
			( new BaseBlockStub() )->block_type_metadata()
		);
	}

	public function test_block_type_classname() {
		$this->assertEquals(
			'wp-block-omniform-base-block-stub',
			( new BaseBlockStub() )->block_type_classname()
		);
	}

	public function test_render_block() {
		$this->assertEquals(
			'BaseBlockStub::render',
			( new BaseBlockStub() )->render_block( array(), '', (object) array() )
		);
	}

	public function test_get_block_attribute() {
		$attributes = array( 'one' => 'value' );

		$block_type = new BaseBlockStub();
		$block_type->render_block( $attributes, '', (object) array() );

		$this->assertEquals( $attributes['one'], $block_type->get_block_attribute( 'one' ) );
		$this->assertNull( $block_type->get_block_attribute( 'two' ) );
	}

	public function test_get_block_context() {
		$context = array( 'context' => array( 'one' => 'value' ) );

		$block_type = new BaseBlockStub();
		$block_type->render_block( array(), '', (object) $context );

		$this->assertEquals( $context['context']['one'], $block_type->get_block_context( 'one' ) );
		$this->assertNull( $block_type->get_block_context( 'two' ) );
	}

	public function test_get_element_attribute() {
		$block_type = new BaseBlockStub();

		$this->assertEquals(
			'id="one two three"',
			$block_type->get_element_attribute( 'id', array( 'one', 'two', 'three' ) )
		);
		$this->assertEquals(
			'id="one"',
			$block_type->get_element_attribute( 'id', 'one' )
		);
		$this->assertNull( $block_type->get_element_attribute( 'id', null ) );
	}
}

// phpcs:disable
class BaseBlockStub extends BaseBlock {
	public function render() {
		return 'BaseBlockStub::render';
	}
}