<?php
/**
 * Tests for Select.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Select;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Select.
 */
class SelectTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Select
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Select();
	}

	/**
	 * Test get_control_name.
	 */
	public function test_get_control_name() {
		$block = $this->createBlockWithContext( array( 'omniform/fieldName' => 'select' ) );

		$this->block->render_block( array( 'isMultiple' => true ), '', $block );
		$this->assertEquals( 'select[]', $this->block->get_control_name() );

		$this->block->render_block( array( 'isMultiple' => false ), '', $block );
		$this->assertEquals( 'select', $this->block->get_control_name() );
	}

	/**
	 * Test render_control.
	 */
	public function test_render_control() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );

		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $attrs ) {
				$attr_str = '';
				foreach ( $attrs as $key => $value ) {
					if ( is_bool( $value ) ) {
						$attr_str .= $value ? ' ' . $key : '';
					} else {
						$attr_str .= ' ' . $key . '="' . $value . '"';
					}
				}
				return trim( $attr_str );
			}
		);

		$block = $this->createBlockWithContext( array( 'omniform/fieldName' => 'test_select' ) );

		// Single select without placeholder.
		$this->block->render_block( array(), '<option>Option 1</option>', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( '<select', $result );
		$this->assertStringContainsString( 'name="test_select"', $result );
		$this->assertStringContainsString( '<option>Option 1</option></select>', $result );
		$this->assertStringNotContainsString( 'multiple', $result );

		// Multiple select.
		$this->block->render_block( array( 'isMultiple' => true ), '<option>Option 1</option>', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( 'multiple', $result );
		$this->assertStringContainsString( 'name="test_select[]"', $result );

		// With placeholder.
		$this->block->render_block( array( 'fieldPlaceholder' => 'Choose...' ), '<option>Option 1</option>', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( '<option value="">Choose...</option><option>Option 1</option>', $result );
	}

	/**
	 * Test get_extra_wrapper_attributes.
	 */
	public function test_get_extra_wrapper_attributes() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );

		$block = $this->createBlockWithContext( array( 'omniform/fieldName' => 'test_select' ) );

		$this->block->render_block( array( 'isMultiple' => true ), '', $block );
		$attributes = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals( 'test_select[]', $attributes['name'] );
		$this->assertTrue( $attributes['multiple'] );

		$this->block->render_block( array( 'isMultiple' => false ), '', $block );
		$attributes = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals( 'test_select', $attributes['name'] );
		$this->assertArrayNotHasKey( 'multiple', $attributes );
	}

	/**
	 * Test get_extra_wrapper_attributes with height for multiple select.
	 */
	public function test_get_extra_wrapper_attributes_with_height() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array( 'style' => 'min-height: 100px;' ) );

		$block = $this->createBlockWithContext( array( 'omniform/fieldName' => 'test_select' ) );

		$this->block->render_block( array( 'isMultiple' => true ), '', $block );
		$attributes = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals( 'height:  100px;', $attributes['style'] );

		$this->block->render_block( array( 'isMultiple' => false ), '', $block );
		$attributes = $this->block->get_extra_wrapper_attributes();
		$this->assertArrayNotHasKey( 'style', $attributes );
	}
}
