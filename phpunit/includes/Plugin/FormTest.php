<?php
/**
 * Tests the Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Plugin;

use OmniForm\Plugin\Form;
use OmniForm\Dependencies\Respect\Validation;

/**
 * Tests the Form class.
 */
class FormTest extends \WP_UnitTestCase {
	/**
	 * Form instance.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * This method is called before each test.
	 */
	public function set_up() {
		parent::set_up();
		$validator  = new Validation\Validator();
		$this->form = new Form( $validator );
	}

	/**
	 * Test sanitize_array with email field type.
	 */
	public function test_sanitize_array_with_email() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'email' => array(
					'label' => 'Email Address',
					'type'  => 'email',
				),
			)
		);

		// Test with valid email.
		$this->form->set_request_params( array( 'email' => 'test@example.com' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 'test@example.com', $params['email'] );

		// Test with invalid email.
		$this->form->set_request_params( array( 'email' => 'invalid-email' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( '', $params['email'] );
	}

	/**
	 * Test sanitize_array with URL field type.
	 */
	public function test_sanitize_array_with_url() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'website' => array(
					'label' => 'Website URL',
					'type'  => 'url',
				),
			)
		);

		// Test with valid URL.
		$this->form->set_request_params( array( 'website' => 'https://example.com' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 'https://example.com', $params['website'] );

		// Test with invalid URL.
		$this->form->set_request_params( array( 'website' => 'javascript:alert(1)' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( '', $params['website'] );
	}

	/**
	 * Test sanitize_array with number field type.
	 */
	public function test_sanitize_array_with_number() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'age' => array(
					'label' => 'Age',
					'type'  => 'number',
				),
			)
		);

		// Test with valid number.
		$this->form->set_request_params( array( 'age' => '25' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 25.0, $params['age'] );

		// Test with decimal number.
		$this->form->set_request_params( array( 'age' => '25.5' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 25.5, $params['age'] );

		// Test with invalid number.
		$this->form->set_request_params( array( 'age' => 'not-a-number' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 0, $params['age'] );
	}

	/**
	 * Test sanitize_array with textarea field type.
	 */
	public function test_sanitize_array_with_textarea() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'message' => array(
					'label' => 'Message',
					'type'  => 'textarea',
				),
			)
		);

		// Test with multiline text.
		$this->form->set_request_params( array( 'message' => "Line 1\nLine 2\nLine 3" ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( "Line 1\nLine 2\nLine 3", $params['message'] );

		// Test with HTML tags (should be stripped).
		$this->form->set_request_params( array( 'message' => '<script>alert(1)</script>Hello' ) );
		$params = $this->form->get_request_params();
		$this->assertStringNotContainsString( '<script>', $params['message'] );
	}

	/**
	 * Test sanitize_array with text field type (default).
	 */
	public function test_sanitize_array_with_text() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'name' => array(
					'label' => 'Name',
					'type'  => 'text',
				),
			)
		);

		// Test with text.
		$this->form->set_request_params( array( 'name' => 'John Doe' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 'John Doe', $params['name'] );

		// Test with HTML tags (should be stripped).
		$this->form->set_request_params( array( 'name' => '<b>John</b> Doe' ) );
		$params = $this->form->get_request_params();
		$this->assertEquals( 'John Doe', $params['name'] );
	}

	/**
	 * Test get_fields returns labels only for backward compatibility.
	 */
	public function test_get_fields_returns_labels() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'email' => array(
					'label' => 'Email Address',
					'type'  => 'email',
				),
				'name'  => array(
					'label' => 'Full Name',
					'type'  => 'text',
				),
			)
		);

		$fields = $this->form->get_fields();

		$this->assertIsArray( $fields );
		$this->assertEquals( 'Email Address', $fields['email'] );
		$this->assertEquals( 'Full Name', $fields['name'] );
	}

	/**
	 * Test nested array sanitization.
	 */
	public function test_sanitize_nested_array() {
		// Use reflection to set fields property.
		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'fields' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			array(
				'contact.email' => array(
					'label' => 'Contact Email',
					'type'  => 'email',
				),
				'contact.name'  => array(
					'label' => 'Contact Name',
					'type'  => 'text',
				),
			)
		);

		// Test with nested data.
		$this->form->set_request_params(
			array(
				'contact' => array(
					'email' => 'test@example.com',
					'name'  => '<b>John Doe</b>',
				),
			)
		);
		$params = $this->form->get_request_params();
		$this->assertEquals( 'test@example.com', $params['contact']['email'] );
		$this->assertEquals( 'John Doe', $params['contact']['name'] );

		// Test with invalid email in nested data.
		$this->form->set_request_params(
			array(
				'contact' => array(
					'email' => 'invalid-email',
					'name'  => 'Jane Doe',
				),
			)
		);
		$params = $this->form->get_request_params();
		$this->assertEquals( '', $params['contact']['email'] );
		$this->assertEquals( 'Jane Doe', $params['contact']['name'] );
	}
}
