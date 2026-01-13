<?php
/**
 * Tests the FormTypesManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\includes\FormTypes;

use OmniForm\FormTypes\FormTypesManager;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the FormTypesManager class.
 */
class FormTypesManagerTest extends BaseTestCase {
	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		// Mock the __() function to return the input string.
		WP_Mock::userFunction(
			'__',
			array(
				'return_arg' => 0,
			)
		);
	}

	/**
	 * Test the constructor initializes form types correctly.
	 */
	public function testConstructorInitializesFormTypes() {
		$manager = new FormTypesManager();

		$form_types = $manager->get_form_types();

		$this->assertCount( 1, $form_types );
		$this->assertEquals( 'standard', $form_types[0]['type'] );
		$this->assertEquals( 'Standard', $form_types[0]['label'] );
		$this->assertEquals( 'A standard form.', $form_types[0]['description'] );
		$this->assertEquals( '', $form_types[0]['icon'] );
	}

	/**
	 * Test the constructor sets the default form type.
	 */
	public function testConstructorSetsDefaultFormType() {
		$manager = new FormTypesManager();

		$this->assertEquals( 'standard', $manager->get_default_form_type() );
	}

	/**
	 * Test adding a form type.
	 */
	public function testAddFormType() {
		$manager = new FormTypesManager();

		$new_form_type = array(
			'type'        => 'custom',
			'label'       => 'Custom Form',
			'description' => 'A custom form type.',
			'icon'        => 'custom-icon',
		);

		$manager->add_form_type( $new_form_type );

		$form_types = $manager->get_form_types();

		$this->assertCount( 2, $form_types );
		$this->assertEquals( $new_form_type, $form_types[1] );
	}

	/**
	 * Test getting form types.
	 */
	public function testGetFormTypes() {
		$manager = new FormTypesManager();

		$form_types = $manager->get_form_types();

		$this->assertIsArray( $form_types );
		$this->assertNotEmpty( $form_types );
	}

	/**
	 * Test getting the default form type.
	 */
	public function testGetDefaultFormType() {
		$manager = new FormTypesManager();

		$default = $manager->get_default_form_type();

		$this->assertIsString( $default );
		$this->assertEquals( 'standard', $default );
	}

	/**
	 * Test validating a valid form type.
	 */
	public function testValidateFormTypeValid() {
		$manager = new FormTypesManager();

		$result = $manager->validate_form_type( 'standard' );

		$this->assertEquals( 'standard', $result );
	}

	/**
	 * Test validating an invalid form type returns default.
	 */
	public function testValidateFormTypeInvalidReturnsDefault() {
		$manager = new FormTypesManager();

		$result = $manager->validate_form_type( 'invalid' );

		$this->assertEquals( 'standard', $result );
	}

	/**
	 * Test validating a custom added form type.
	 */
	public function testValidateFormTypeCustom() {
		$manager = new FormTypesManager();

		$manager->add_form_type(
			array(
				'type'        => 'custom',
				'label'       => 'Custom',
				'description' => 'Custom form',
				'icon'        => '',
			)
		);

		$result = $manager->validate_form_type( 'custom' );

		$this->assertEquals( 'custom', $result );
	}
}
