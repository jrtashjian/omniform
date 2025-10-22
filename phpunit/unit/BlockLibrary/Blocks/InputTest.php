<?php
/**
 * Tests for Input.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Input;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Input.
 */
class InputTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Input
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		\WP_Mock::userFunction( 'get_query_var' )->andReturn( 'query_value' );

		$this->block = new Input();
	}

	/**
	 * Data provider for test_get_type.
	 */
	public function data_get_type() {
		return array(
			array( 'email', true, 2, array( 'OmniForm\\Dependencies\\Respect\\Validation\\Rules\\NotEmpty', 'OmniForm\\Dependencies\\Respect\\Validation\\Rules\\Email' ) ), // NotEmpty and Email.
			array( 'email', false, 1, array( 'OmniForm\\Dependencies\\Respect\\Validation\\Rules\\Optional' ) ), // Optional(Email).
			array( 'text', true, 1, array( 'OmniForm\\Dependencies\\Respect\\Validation\\Rules\\NotEmpty' ) ),  // NotEmpty.
		);
	}

	/**
	 * Test get_type.
	 *
	 * @param string $field_type The field type.
	 * @param bool   $is_required Whether the field is required.
	 * @param int    $expected_count The expected rule count.
	 * @param array  $expected_classes The expected rule classes.
	 * @dataProvider data_get_type
	 */
	public function test_get_type( $field_type, $is_required, $expected_count, $expected_classes ) {
		$this->block->render_block(
			array( 'fieldType' => $field_type ),
			'',
			$this->createBlockWithContext( array( 'omniform/fieldIsRequired' => $is_required ) )
		);
		$rules = $this->block->get_validation_rules();
		$this->assertCount( $expected_count, $rules );
		foreach ( $rules as $index => $rule ) {
			$this->assertEquals( $expected_classes[ $index ], get_class( $rule ) );
		}
	}

	/**
	 * Data provider for test_get_control_name_parts.
	 */
	public function data_get_control_name_parts() {
		return array(
			array( 'radio', array( 'group' ) ),
			array( 'text', array( 'group', 'field' ) ),
		);
	}

	/**
	 * Test get_control_name_parts.
	 *
	 * @param string $field_type The field type.
	 * @param array  $expected_parts The expected name parts.
	 * @dataProvider data_get_control_name_parts
	 */
	public function test_get_control_name_parts( $field_type, $expected_parts ) {
		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldGroupName' => 'group',
				'omniform/fieldName'      => 'field',
			)
		);

		$this->block->render_block( array( 'fieldType' => $field_type ), '', $block );
		$this->assertEquals( $expected_parts, $this->block->get_control_name_parts() );
	}

	/**
	 * Data provider for test_get_control_name.
	 */
	public function data_get_control_name() {
		return array(
			array( 'checkbox', 'group[]' ),
			array( 'text', 'group[field]' ),
		);
	}

	/**
	 * Test get_control_name.
	 *
	 * @param string $field_type The field type.
	 * @param string $expected_name The expected control name.
	 * @dataProvider data_get_control_name
	 */
	public function test_get_control_name( $field_type, $expected_name ) {
		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldGroupName' => 'group',
				'omniform/fieldName'      => 'field',
			)
		);

		$this->block->render_block( array( 'fieldType' => $field_type ), '', $block );
		$this->assertEquals( $expected_name, $this->block->get_control_name() );
	}

	/**
	 * Test get_extra_wrapper_attributes.
	 */
	public function test_get_extra_wrapper_attributes() {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );
		$mock_instance->shouldReceive( 'get_instance' )->andReturn( $mock_instance );

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldLabel' => 'Field Label',
				'omniform/fieldName'  => 'field_name',
			)
		);

		$mock_block = \Mockery::mock( $this->block );
		$mock_block->shouldReceive( 'get_extra_wrapper_attributes' )->passthru();
		$mock_block->shouldReceive( 'get_control_value' )->andReturn( 'test_value' );
		$mock_block->shouldReceive( 'get_control_name' )->andReturn( 'test_name' );
		$mock_block->shouldReceive( 'is_required' )->andReturn( true );

		$mock_block->render_block( array( 'fieldType' => 'text' ), '', $block );
		$attributes = $mock_block->get_extra_wrapper_attributes();
		$this->assertEquals( 'text', $attributes['type'] );
		$this->assertEquals( 'Field Label', $attributes['aria-label'] );

		$mock_block->render_block( array( 'fieldType' => 'username-email' ), '', $block );
		$attributes = $mock_block->get_extra_wrapper_attributes();
		$this->assertEquals( 'text', $attributes['type'] );

		$mock_block->render_block(
			array(
				'fieldType'        => 'email',
				'fieldPlaceholder' => 'Enter email',
			),
			'',
			$block
		);
		$attributes = $mock_block->get_extra_wrapper_attributes();
		$this->assertEquals( 'email', $attributes['type'] );
		$this->assertEquals( 'Enter email', $attributes['placeholder'] );
	}

	/**
	 * Data provider for test_get_control_value.
	 */
	public function data_get_control_value() {
		return array(
			array( array( 'fieldValue' => 'custom_value' ), 'custom_value' ),
			array( array( 'fieldType' => 'checkbox' ), 'Test Label' ),
			array( array( 'fieldType' => 'radio' ), 'Test Label' ),
			array( array( 'fieldType' => 'date' ), null, '/^\d{4}-\d{2}-\d{2}$/' ),
			array( array( 'fieldType' => 'time' ), null, '/^\d{2}:\d{2}:\d{2}$/' ),
			array( array( 'fieldType' => 'month' ), null, '/^\d{4}-\d{2}$/' ),
			array( array( 'fieldType' => 'week' ), null, '/^\d{4}-W\d{2}$/' ),
			array( array( 'fieldType' => 'datetime-local' ), null, '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/' ),
			array( array( 'fieldType' => 'search' ), 'query_value' ),
			array( array( 'fieldType' => 'text' ), '' ),
		);
	}

	/**
	 * Test get_control_value.
	 *
	 * @param array       $attributes The block attributes.
	 * @param string|null $expected The expected value.
	 * @param string|null $pattern The regex pattern to match.
	 * @dataProvider data_get_control_value
	 */
	public function test_get_control_value( $attributes, $expected = null, $pattern = null ) {
		$mock_instance = \Mockery::mock( 'overload:WP_Block_Supports' );
		$mock_instance->shouldReceive( 'apply_block_supports' )->andReturn( array() );
		$mock_instance->shouldReceive( 'get_instance' )->andReturn( $mock_instance );

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldLabel' => 'Test Label',
				'omniform/fieldName'  => 'test_field',
			)
		);

		$this->block->render_block( $attributes, '', $block );
		$value = $this->block->get_control_value();

		if ( $pattern ) {
			$this->assertMatchesRegularExpression( $pattern, $value );
		} else {
			$this->assertEquals( $expected, $value );
		}
	}
}
