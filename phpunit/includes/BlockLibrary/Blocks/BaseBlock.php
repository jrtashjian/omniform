<?php
/**
 * Tests the Core class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseBlock;

/**
 * Tests the Core class.
 */
class BaseBlockTest extends \WP_UnitTestCase {
	public function test_block_type_metadata() {
		$this->assertEquals(
			'/build/block-library/base-block-stub',
			( new BaseBlockStub() )->blockTypeMetadata()
		);
	}

	public function test_block_type_class_name() {
		$this->assertEquals(
			'wp-block-omniform-base-block-stub',
			( new BaseBlockStub() )->blockTypeClassName()
		);
	}

	public function test_render_block_with_attributes() {
		$this->assertEquals(
			'BaseBlockStub::render',
			( new BaseBlockStub() )->renderBlock( array(), '', (object) array() )
		);
	}

	public function test_get_block_attribute() {
		$attributes = array( 'one' => 'value' );

		$block_type = new BaseBlockStub();
		$block_type->renderBlock( $attributes, '', (object) array() );

		$this->assertEquals( $attributes['one'], $block_type->getBlockAttribute( 'one' ) );
		$this->assertNull( $block_type->getBlockAttribute( 'two' ) );
	}

	public function test_get_block_context() {
		$context = array( 'context' => array( 'one' => 'value' ) );

		$block_type = new BaseBlockStub();
		$block_type->renderBlock( array(), '', (object) $context );

		$this->assertEquals( $context['context']['one'], $block_type->getBlockContext( 'one' ) );
		$this->assertNull( $block_type->getBlockContext( 'two' ) );
	}

	public function test_get_element_attribute() {
		$block_type = new BaseBlockStub();

		$this->assertEquals(
			'id="one two three"',
			$block_type->getElementAttribute( 'id', array( 'one', 'two', 'three' ) )
		);
		$this->assertEquals(
			'id="one"',
			$block_type->getElementAttribute( 'id', 'one' )
		);
		$this->assertNull( $block_type->getElementAttribute( 'id', null ) );
	}
}

// phpcs:disable
class BaseBlockStub extends BaseBlock {
	public function render() {
		return 'BaseBlockStub::render';
	}
}