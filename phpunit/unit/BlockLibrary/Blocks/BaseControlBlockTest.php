<?php
/**
 * Tests for BaseControlBlock.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Concrete implementation for testing BaseControlBlock.
 */
class TestBaseControlBlock extends BaseControlBlock {
	public function render_control() {
		return 'control output';
	}
}

/**
 * Tests for BaseControlBlock.
 */
class BaseControlBlockTest extends BaseTestCase {

	/**
	 * Test block instance.
	 *
	 * @var BaseControlBlock
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		\WP_Mock::userFunction( 'sanitize_html_class' )->andReturnUsing(
			function ( $str ) {
				return strtolower( str_replace( ' ', '-', $str ) );
			}
		);

		$this->block = new TestBaseControlBlock();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$block_with_label          = \Mockery::mock( '\WP_Block' );
		$block_with_label->context = array( 'omniform/fieldLabel' => 'Test Label' );

		$this->block->render_block( array(), '', $block_with_label );
		$this->assertEquals( 'control output', $this->block->render() );

		$block_without_label = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block_without_label );
		$this->assertEquals( '', $this->block->render() );
	}

	/**
	 * Test get_field_label.
	 */
	public function test_get_field_label() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldLabel' => 'Label' );

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'Label', $this->block->get_field_label() );
	}

	/**
	 * Test get_field_name.
	 */
	public function test_get_field_name() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldName' => 'field name' );

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'field-name', $this->block->get_field_name() );

		$block2          = \Mockery::mock( '\WP_Block' );
		$block2->context = array( 'omniform/fieldLabel' => 'Field Label' );

		$this->block->render_block( array(), '', $block2 );
		$this->assertEquals( 'field-label', $this->block->get_field_name() );
	}

	/**
	 * Test get_field_group_label.
	 */
	public function test_get_field_group_label() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldGroupLabel' => 'Group Label' );

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'Group Label', $this->block->get_field_group_label() );
	}

	/**
	 * Test get_field_group_name.
	 */
	public function test_get_field_group_name() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldGroupName' => 'group name' );

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'group-name', $this->block->get_field_group_name() );
	}

	/**
	 * Test is_grouped.
	 */
	public function test_is_grouped() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldGroupName' => 'group' );

		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->is_grouped() );

		$block2 = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block2 );
		$this->assertFalse( $this->block->is_grouped() );
	}

	/**
	 * Test is_required.
	 */
	public function test_is_required() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->is_required() );

		$block2          = \Mockery::mock( '\WP_Block' );
		$block2->context = array( 'omniform/fieldGroupIsRequired' => true );

		$this->block->render_block( array(), '', $block2 );
		$this->assertTrue( $this->block->is_required() );

		$block3 = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block3 );
		$this->assertFalse( $this->block->is_required() );
	}

	/**
	 * Test get_control_name_parts.
	 */
	public function test_get_control_name_parts() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array(
			'omniform/fieldGroupName' => 'group',
			'omniform/fieldName'      => 'field',
		);

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( array( 'group', 'field' ), $this->block->get_control_name_parts() );

		$block2          = \Mockery::mock( '\WP_Block' );
		$block2->context = array( 'omniform/fieldName' => 'field' );

		$this->block->render_block( array(), '', $block2 );
		$this->assertEquals( array( 'field' ), $this->block->get_control_name_parts() );
	}

	/**
	 * Test get_control_name.
	 */
	public function test_get_control_name() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array(
			'omniform/fieldGroupName' => 'group',
			'omniform/fieldName'      => 'field',
		);

		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'group[field]', $this->block->get_control_name() );

		$block2          = \Mockery::mock( '\WP_Block' );
		$block2->context = array( 'omniform/fieldName' => 'field' );

		$this->block->render_block( array(), '', $block2 );
		$this->assertEquals( 'field', $this->block->get_control_name() );
	}

	/**
	 * Test get_control_value.
	 */
	public function test_get_control_value() {
		$this->block->render_block( array( 'fieldValue' => 'value' ), '', \Mockery::mock( '\WP_Block' ) );
		$this->assertEquals( 'value', $this->block->get_control_value() );

		$this->block->render_block( array(), '', \Mockery::mock( '\WP_Block' ) );
		$this->assertEquals( '', $this->block->get_control_value() );

		// Test with callback.
		\WP_Mock::userFunction( 'test_callback' )->andReturn( 'callback result' );
		\WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
		$this->block->render_block( array( 'fieldValue' => '{{ test_callback }}' ), '', \Mockery::mock( '\WP_Block' ) );
		$this->assertEquals( 'callback result', $this->block->get_control_value() );
	}

	/**
	 * Test get_validation_rules.
	 */
	public function test_get_validation_rules() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );
		$rules = $this->block->get_validation_rules();
		$this->assertCount( 1, $rules );
		$this->assertInstanceOf( \OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty::class, $rules[0] );

		$block2 = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block2 );
		$this->assertEmpty( $this->block->get_validation_rules() );
	}

	/**
	 * Test has_validation_rules.
	 */
	public function test_has_validation_rules() {
		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->has_validation_rules() );

		$block2 = \Mockery::mock( '\WP_Block' );
		$this->block->render_block( array(), '', $block2 );
		$this->assertFalse( $this->block->has_validation_rules() );
	}

	/**
	 * Test get_extra_wrapper_attributes.
	 */
	public function test_get_extra_wrapper_attributes() {
		$mock = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock->shouldReceive( 'get_instance' )->andReturnSelf();
		$mock->shouldReceive( 'apply_block_supports' )->andReturn( array() );

		$block          = \Mockery::mock( '\WP_Block' );
		$block->context = array(
			'omniform/fieldName'       => 'test-field',
			'omniform/fieldIsRequired' => true,
		);

		$this->block->render_block( array( 'fieldValue' => 'test value' ), '', $block );

		$result = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals(
			array(
				'id'       => 'test-field',
				'name'     => 'test-field',
				'value'    => 'test value',
				'required' => true,
			),
			$result
		);
	}
}
