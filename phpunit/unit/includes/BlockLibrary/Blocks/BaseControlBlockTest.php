<?php
/**
 * Tests the BaseControlBlock class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;
use WP_Mock;
use Mockery;

/**
 * Tests the BaseControlBlock class.
 */
class BaseControlBlockTest extends \OmniForm\Tests\Unit\BaseTestCase {
	/**
	 * Test instance.
	 *
	 * @var TestableBaseControlBlock
	 */
	private $block;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		// Mock WordPress functions.
		WP_Mock::userFunction(
			'sanitize_html_class',
			array(
				'return' => function ( $input ) {
					return str_replace( ' ', '-', $input );
				},
			)
		);

		WP_Mock::userFunction(
			'esc_attr',
			array(
				'return' => function ( $text ) {
					return htmlspecialchars( $text, ENT_QUOTES );
				},
			)
		);

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );

		$this->block = new TestableBaseControlBlock();
	}

	/**
	 * Test render method when field label exists.
	 */
	public function testRenderWithFieldLabel() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldLabel' => 'Test Label' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( '<div>Test Block Control</div>', $this->block->render() );
	}

	/**
	 * Test render method when no field label exists.
	 */
	public function testRenderWithoutFieldLabel() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( '', $this->block->render() );
	}

	/**
	 * Test get_field_label method.
	 */
	public function testGetFieldLabel() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldLabel' => 'Test Label' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'Test Label', $this->block->get_field_label() );
	}

	/**
	 * Test get_field_name method with field name context.
	 */
	public function testGetFieldNameWithContext() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldName' => 'test field name' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'test-field-name', $this->block->get_field_name() );
	}

	/**
	 * Test get_field_name method with field label fallback.
	 */
	public function testGetFieldNameWithLabelFallback() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldLabel' => 'Test Label' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'Test-Label', $this->block->get_field_name() );
	}

	/**
	 * Test get_field_group_label method.
	 */
	public function testGetFieldGroupLabel() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldGroupLabel' => 'Group Label' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'Group Label', $this->block->get_field_group_label() );
	}

	/**
	 * Test get_field_group_name method with group name context.
	 */
	public function testGetFieldGroupNameWithContext() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldGroupName' => 'test group name' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'test-group-name', $this->block->get_field_group_name() );
	}

	/**
	 * Test get_field_group_name method with group label fallback.
	 */
	public function testGetFieldGroupNameWithLabelFallback() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldGroupLabel' => 'Group Label' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'Group-Label', $this->block->get_field_group_name() );
	}

	/**
	 * Test is_grouped method when grouped.
	 */
	public function testIsGroupedTrue() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldGroupName' => 'group' );

		$this->block->render_block( array(), '', $block );

		$this->assertTrue( $this->block->is_grouped() );
	}

	/**
	 * Test is_grouped method when not grouped.
	 */
	public function testIsGroupedFalse() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$this->assertFalse( $this->block->is_grouped() );
	}

	/**
	 * Test is_required method with field group required.
	 */
	public function testIsRequiredWithGroupRequired() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldGroupIsRequired' => true );

		$this->block->render_block( array(), '', $block );

		$this->assertTrue( $this->block->is_required() );
	}

	/**
	 * Test is_required method with field required.
	 */
	public function testIsRequiredWithFieldRequired() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );

		$this->assertTrue( $this->block->is_required() );
	}

	/**
	 * Test is_required method when not required.
	 */
	public function testIsRequiredFalse() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$this->assertFalse( $this->block->is_required() );
	}

	/**
	 * Test get_control_name_parts method.
	 */
	public function testGetControlNameParts() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array(
			'omniform/fieldGroupName' => 'group',
			'omniform/fieldName'      => 'field',
		);

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_control_name_parts();

		$this->assertEquals( array( 'group', 'field' ), $result );
	}

	/**
	 * Test get_control_name method for grouped field.
	 */
	public function testGetControlNameGrouped() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array(
			'omniform/fieldGroupName' => 'group',
			'omniform/fieldName'      => 'field',
		);

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'group[field]', $this->block->get_control_name() );
	}

	/**
	 * Test get_control_name method for single field.
	 */
	public function testGetControlNameSingle() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldName' => 'field' );

		$this->block->render_block( array(), '', $block );

		$this->assertEquals( 'field', $this->block->get_control_name() );
	}

	/**
	 * Test get_control_value method with simple value.
	 */
	public function testGetControlValueSimple() {
		$this->block->render_block(
			array( 'fieldValue' => 'test value' ),
			'',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( 'test value', $this->block->get_control_value() );
	}

	/**
	 * Test get_control_value method with callback.
	 */
	public function testGetControlValueWithCallback() {
		// Mock a simple function for testing.
		WP_Mock::userFunction(
			'test_callback',
			array(
				'return' => 'callback result',
			)
		);

		$this->block->render_block(
			array( 'fieldValue' => '{{ test_callback }}' ),
			'',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( 'callback result', $this->block->get_control_value() );
	}

	/**
	 * Test get_validation_rules method when required.
	 */
	public function testGetValidationRulesRequired() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_validation_rules();

		$this->assertCount( 1, $result );
		$this->assertInstanceOf( \OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty::class, $result[0] );
	}

	/**
	 * Test get_validation_rules method when not required.
	 */
	public function testGetValidationRulesNotRequired() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$this->assertEmpty( $this->block->get_validation_rules() );
	}

	/**
	 * Test has_validation_rules method when rules exist.
	 */
	public function testHasValidationRulesTrue() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldIsRequired' => true );

		$this->block->render_block( array(), '', $block );

		$this->assertTrue( $this->block->has_validation_rules() );
	}

	/**
	 * Test has_validation_rules method when no rules exist.
	 */
	public function testHasValidationRulesFalse() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$result = $this->block->has_validation_rules();

		$this->assertFalse( $result );
	}

	/**
	 * Test get_extra_wrapper_attributes method with required field.
	 */
	public function testGetExtraWrapperAttributesWithRequired() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array(
			'omniform/fieldLabel'      => 'Test Field',
			'omniform/fieldIsRequired' => true,
		);

		$this->block->render_block(
			array( 'fieldValue' => 'test value' ),
			'',
			$block
		);

		$result = $this->block->get_extra_wrapper_attributes();

		$expected = array(
			'id'       => 'Test-Field',
			'name'     => 'Test-Field',
			'value'    => 'test value',
			'required' => true,
		);

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test get_extra_wrapper_attributes method with grouped field.
	 */
	public function testGetExtraWrapperAttributesWithGroupedField() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array(
			'omniform/fieldGroupName' => 'group',
			'omniform/fieldName'      => 'field',
		);

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_extra_wrapper_attributes();

		$expected = array(
			'id'   => 'field', // get_field_name() returns field name, not group name.
			'name' => 'group[field]',
		);

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test get_extra_wrapper_attributes method filters empty values.
	 */
	public function testGetExtraWrapperAttributesFiltersEmptyValues() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'omniform/fieldLabel' => 'Test Field' );

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_extra_wrapper_attributes();

		// Should only contain non-empty values.
		$this->assertArrayHasKey( 'id', $result );
		$this->assertArrayHasKey( 'name', $result );
		$this->assertArrayNotHasKey( 'value', $result ); // Empty string is filtered out.
		$this->assertArrayNotHasKey( 'required', $result ); // False values are filtered out.
		$this->assertArrayNotHasKey( 'class', $result ); // Empty string is filtered out.
	}
}

// phpcs:disable
class TestableBaseControlBlock extends BaseControlBlock {
	public function render_control() {
		return '<div>Test Block Control</div>';
	}
}
