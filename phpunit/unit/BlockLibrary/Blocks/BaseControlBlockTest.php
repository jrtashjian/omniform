<?php
/**
 * Tests for BaseControlBlock.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for BaseControlBlock.
 */
class BaseControlBlockTest extends BaseBlockTestCase {

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

		$this->block = new TestBaseControlBlock();
	}

	/**
	 * Data provider for context getter tests.
	 */
	public function contextGetterDataProvider() {
		return array(
			'get_field_label'                => array(
				'context'  => array( 'omniform/fieldLabel' => 'Label' ),
				'method'   => 'get_field_label',
				'expected' => 'Label',
			),
			'get_field_name from fieldName'  => array(
				'context'  => array( 'omniform/fieldName' => 'field name' ),
				'method'   => 'get_field_name',
				'expected' => 'field-name',
			),
			'get_field_name from fieldLabel' => array(
				'context'  => array( 'omniform/fieldLabel' => 'Field Label' ),
				'method'   => 'get_field_name',
				'expected' => 'field-label',
			),
			'get_field_group_label'          => array(
				'context'  => array( 'omniform/fieldGroupLabel' => 'Group Label' ),
				'method'   => 'get_field_group_label',
				'expected' => 'Group Label',
			),
			'get_field_group_name'           => array(
				'context'  => array( 'omniform/fieldGroupName' => 'group name' ),
				'method'   => 'get_field_group_name',
				'expected' => 'group-name',
			),
		);
	}

	/**
	 * Test context getters.
	 *
	 * @dataProvider contextGetterDataProvider
	 * @param array  $context  Block context.
	 * @param string $method   Method name.
	 * @param string $expected Expected result.
	 */
	public function test_context_getters( $context, $method, $expected ) {
		$block = $this->createBlockWithContext( $context );
		$this->block->render_block( array(), '', $block );
		$this->assertEquals( $expected, $this->block->{$method}() );
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$block_with_label = $this->createBlockWithContext( array( 'omniform/fieldLabel' => 'Test Label' ) );
		$result           = $this->block->render_block( array(), '', $block_with_label );
		$this->assertEquals( 'control output', $result );

		$block_without_label = $this->createBlockWithContext();
		$result              = $this->block->render_block( array(), '', $block_without_label );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test is_grouped.
	 */
	public function test_is_grouped() {
		$block = $this->createBlockWithContext( array( 'omniform/fieldGroupName' => 'group' ) );
		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->is_grouped() );

		$block2 = $this->createBlockWithContext();
		$this->block->render_block( array(), '', $block2 );
		$this->assertFalse( $this->block->is_grouped() );
	}

	/**
	 * Test is_required.
	 */
	public function test_is_required() {
		$block = $this->createBlockWithContext( array( 'omniform/fieldIsRequired' => true ) );
		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->is_required() );

		$block2 = $this->createBlockWithContext( array( 'omniform/fieldGroupIsRequired' => true ) );
		$this->block->render_block( array(), '', $block2 );
		$this->assertTrue( $this->block->is_required() );

		$block3 = $this->createBlockWithContext();
		$this->block->render_block( array(), '', $block3 );
		$this->assertFalse( $this->block->is_required() );
	}

	/**
	 * Test get_control_name_parts.
	 */
	public function test_get_control_name_parts() {
		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldGroupName' => 'group',
				'omniform/fieldName'      => 'field',
			)
		);
		$this->block->render_block( array(), '', $block );
		$this->assertEquals( array( 'group', 'field' ), $this->block->get_control_name_parts() );

		$block2 = $this->createBlockWithContext( array( 'omniform/fieldName' => 'field' ) );
		$this->block->render_block( array(), '', $block2 );
		$this->assertEquals( array( 'field' ), $this->block->get_control_name_parts() );
	}

	/**
	 * Test get_control_name.
	 */
	public function test_get_control_name() {
		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldGroupName' => 'group',
				'omniform/fieldName'      => 'field',
			)
		);
		$this->block->render_block( array(), '', $block );
		$this->assertEquals( 'group[field]', $this->block->get_control_name() );

		$block2 = $this->createBlockWithContext( array( 'omniform/fieldName' => 'field' ) );
		$this->block->render_block( array(), '', $block2 );
		$this->assertEquals( 'field', $this->block->get_control_name() );
	}

	/**
	 * Test get_control_value.
	 */
	public function test_get_control_value() {
		$this->block->render_block( array( 'fieldValue' => 'value' ), '', $this->createBlockWithContext() );
		$this->assertEquals( 'value', $this->block->get_control_value() );

		$this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $this->block->get_control_value() );

		// Test with callback.
		\WP_Mock::userFunction( 'test_callback' )->andReturn( 'callback result' );
		$this->block->render_block( array( 'fieldValue' => '{{ test_callback }}' ), '', $this->createBlockWithContext() );
		$this->assertEquals( 'callback result', $this->block->get_control_value() );
	}

	/**
	 * Test get_validation_rules.
	 */
	public function test_get_validation_rules() {
		$block = $this->createBlockWithContext( array( 'omniform/fieldIsRequired' => true ) );
		$this->block->render_block( array(), '', $block );
		$rules = $this->block->get_validation_rules();
		$this->assertCount( 1, $rules );
		$this->assertInstanceOf( \OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty::class, $rules[0] );

		$block2 = $this->createBlockWithContext();
		$this->block->render_block( array(), '', $block2 );
		$this->assertEmpty( $this->block->get_validation_rules() );
	}

	/**
	 * Test has_validation_rules.
	 */
	public function test_has_validation_rules() {
		$block = $this->createBlockWithContext( array( 'omniform/fieldIsRequired' => true ) );
		$this->block->render_block( array(), '', $block );
		$this->assertTrue( $this->block->has_validation_rules() );

		$block2 = $this->createBlockWithContext();
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

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldName'       => 'test-field',
				'omniform/fieldIsRequired' => true,
			)
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

// phpcs:disable

/**
 * Concrete implementation for testing BaseControlBlock.
 */
class TestBaseControlBlock extends BaseControlBlock {
	public function render_control() {
		return 'control output';
	}
}
