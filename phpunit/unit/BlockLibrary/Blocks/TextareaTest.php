<?php
/**
 * Tests for Textarea.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Textarea;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Textarea.
 */
class TextareaTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Textarea
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		\WP_Mock::userFunction( 'esc_textarea' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);

		$this->block = new Textarea();
	}

	/**
	 * Test block instance.
	 */
	public function test_instance() {
		$this->assertInstanceOf( Textarea::class, $this->block );
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
					$attr_str .= ' ' . $key . '="' . $value . '"';
				}
				return trim( $attr_str );
			}
		);

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldName'  => 'test_textarea',
				'omniform/fieldLabel' => 'Test Label',
			)
		);

		$this->block->render_block( array(), '', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( '<textarea', $result );
		$this->assertStringContainsString( 'name="test_textarea"', $result );
		$this->assertStringContainsString( '</textarea>', $result );

		// With value.
		$this->block->render_block( array( 'fieldValue' => 'Test content' ), '', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( 'Test content', $result );

		// With placeholder.
		$this->block->render_block( array( 'fieldPlaceholder' => 'Enter text...' ), '', $block );
		$result = $this->block->render_control();
		$this->assertStringContainsString( 'placeholder="Enter text..."', $result );
	}

	/**
	 * Test get_extra_wrapper_attributes.
	 */
	public function test_get_extra_wrapper_attributes() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldName'  => 'test_textarea',
				'omniform/fieldLabel' => 'Textarea Label',
			)
		);

		$this->block->render_block( array( 'fieldPlaceholder' => 'Placeholder' ), '', $block );
		$attributes = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals( 'test_textarea', $attributes['name'] );
		$this->assertEquals( 'Placeholder', $attributes['placeholder'] );
		$this->assertEquals( 'Textarea Label', $attributes['aria-label'] );
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );

		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="textarea-wrapper"' );

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldLabel' => 'Textarea Field',
				'omniform/fieldName'  => 'test_textarea',
			)
		);

		$result = $this->block->render_block( array(), '', $block );
		$this->assertStringContainsString( '<textarea class="textarea-wrapper">', $result );
		$this->assertStringContainsString( '</textarea>', $result );

		// No label.
		$result = $this->block->render_block( array(), '', $this->createBlockWithContext( array() ) );
		$this->assertEquals( '', $result );
	}
}
