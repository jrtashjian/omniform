<?php
/**
 * Tests the Input class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Input;

/**
 * Tests the Input class.
 */
class InputTest extends FormBlockTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\Input
	 */
	protected $block_instance;

	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new InputBlock() );
	}

	/**
	 * Make sure the block does not render markup if the fieldLabel attribute is empty.
	 */
	public function test_does_not_render_without_field_label() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->assertNotEmpty( $this->render_block_with_attributes() );
	}

	/**
	 * Test get_validation_rules for email field type.
	 */
	public function test_get_validation_rules_email() {
		$this->apply_block_context( 'omniform/fieldIsRequired', true );
		$this->render_block_with_attributes( array( 'fieldType' => 'email' ) );

		$rules = $this->block_instance->get_validation_rules();

		$this->assertCount( 2, $rules );
		$this->assertInstanceOf( 'OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty', $rules[0] );
		$this->assertInstanceOf( 'OmniForm\Dependencies\Respect\Validation\Rules\Email', $rules[1] );
	}

	/**
	 * Test get_validation_rules for username-email field type.
	 */
	public function test_get_validation_rules_username_email() {
		$this->apply_block_context( 'omniform/fieldIsRequired', true );
		$this->render_block_with_attributes( array( 'fieldType' => 'username-email' ) );

		$rules = $this->block_instance->get_validation_rules();

		$this->assertCount( 2, $rules );
		$this->assertInstanceOf( 'OmniForm\Dependencies\Respect\Validation\Rules\NotEmpty', $rules[0] );
		$this->assertInstanceOf( 'OmniForm\Validation\Rules\UsernameOrEmailRule', $rules[1] );
	}

	/**
	 * Test get_control_value for various field types (checkbox, radio, date).
	 */
	public function test_get_control_value() {
		$field_label = 'field label';
		$field_value = 'field value';

		$this->apply_block_context( 'omniform/fieldLabel', $field_label );

		foreach ( array( 'checkbox', 'radio' ) as $field_type ) {
			$this->render_block_with_attributes( array( 'fieldType' => $field_type ) );
			$this->assertEquals( $this->block_instance->get_control_value(), $field_label );

			$this->render_block_with_attributes(
				array(
					'fieldType'  => $field_type,
					'fieldValue' => $field_value,
				)
			);
			$this->assertEquals( $this->block_instance->get_control_value(), $field_value );
		}

		$this->render_block_with_attributes( array( 'fieldType' => 'date' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), gmdate( $this->block_instance::FORMAT_DATE ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'time' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), gmdate( $this->block_instance::FORMAT_TIME ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'month' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), gmdate( $this->block_instance::FORMAT_MONTH ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'week' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), gmdate( $this->block_instance::FORMAT_WEEK ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'datetime-local' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), gmdate( $this->block_instance::FORMAT_DATETIME_LOCAL ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'search' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), '' );

		global $wp_query;
		$wp_query->set( $this->block_instance->get_control_name(), 'search query' );
		$this->render_block_with_attributes( array( 'fieldType' => 'search' ) );
		$this->assertEquals( $this->block_instance->get_control_value(), 'search query' );
	}

	/**
	 * Ensure fieldLabel and fieldGroupLabel are returned in the control name parts.
	 * Checkbox and radio fields should not return fieldLabel when fieldGroupLabel is present.
	 */
	public function test_get_control_name_parts() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );

		$this->render_block_with_attributes( array( 'fieldType' => 'text' ) );
		$this->assertTrue( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'checkbox' ) );
		$this->assertTrue( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'radio' ) );
		$this->assertTrue( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );

		$this->apply_block_context( 'omniform/fieldGroupLabel', 'field group label' );

		$this->render_block_with_attributes( array( 'fieldType' => 'text' ) );
		$this->assertTrue( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );
		$this->assertTrue( in_array( 'field-group-label', $this->block_instance->get_control_name_parts(), true ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'checkbox' ) );
		$this->assertFalse( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );
		$this->assertTrue( in_array( 'field-group-label', $this->block_instance->get_control_name_parts(), true ) );

		$this->render_block_with_attributes( array( 'fieldType' => 'radio' ) );
		$this->assertFalse( in_array( 'field-label', $this->block_instance->get_control_name_parts(), true ) );
		$this->assertTrue( in_array( 'field-group-label', $this->block_instance->get_control_name_parts(), true ) );
	}
}

// phpcs:disable
class InputBlock extends Input {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}