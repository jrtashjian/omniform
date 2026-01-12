<?php
/**
 * Tests the Response class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\Response;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the Response class.
 */
class ResponseTest extends BaseTestCase {
	/**
	 * The Response instance.
	 *
	 * @var Response
	 */
	private $response;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		$this->response = new Response();
	}

	/**
	 * Test text_content.
	 */
	public function testTextContent() {
		$this->response->set_request_params(
			array(
				'name'  => 'John Doe',
				'email' => 'john@example.com',
			)
		);
		$this->response->set_fields(
			array(
				'name'  => 'Name',
				'email' => 'Email',
			)
		);

		WP_Mock::userFunction(
			'esc_html',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		WP_Mock::userFunction(
			'wp_kses_post',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		// nl2br is internal PHP function, cannot mock.

		$content = $this->response->text_content();

		$this->assertStringContainsString( 'Name:', $content );
		$this->assertStringContainsString( 'Email:', $content );
	}

	/**
	 * Test email_content.
	 */
	public function testEmailContent() {
		$this->response->set_request_params(
			array(
				'name'              => 'John Doe',
				'email'             => 'john@example.com',
				'_omniform_user_ip' => '192.168.1.1',
				'_wp_http_referer'  => 'http://example.com/form',
			)
		);
		$this->response->set_fields(
			array(
				'name'  => 'Name',
				'email' => 'Email',
			)
		);
		$this->response->set_date( '2023-01-01 12:00:00' );

		WP_Mock::userFunction(
			'__',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		WP_Mock::userFunction(
			'esc_html',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		WP_Mock::userFunction(
			'esc_url',
			array(
				'return' => function ( $url ) {
					return $url;
				},
			)
		);
		WP_Mock::userFunction(
			'wp_kses',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => function ( $text ) {
					return $text;
				},
			)
		);
		WP_Mock::userFunction(
			'get_bloginfo',
			array(
				'return' => 'http://example.com',
			)
		);

		$content = $this->response->email_content();

		$this->assertStringContainsString( 'Name: John Doe', $content );
		$this->assertStringContainsString( 'Email: john@example.com', $content );
		$this->assertStringContainsString( 'IP Address: 192.168.1.1', $content );
	}

	/**
	 * Test get_response_data.
	 */
	public function testGetResponseData() {
		$this->response->set_request_params(
			array(
				'name'  => 'John Doe',
				'email' => 'john@example.com',
				'id'    => 1, // This should be filtered out.
			)
		);
		$this->response->set_fields(
			array(
				'name'  => 'Name',
				'email' => 'Email',
			)
		);

		$data = $this->response->get_response_data();

		$this->assertArrayHasKey( 'content', $data );
		$this->assertArrayHasKey( 'fields', $data );
		$this->assertEquals( 'John Doe', $data['content']->get( 'name' ) );
		$this->assertEquals( 'Name', $data['fields']['name'] );
	}

	/**
	 * Test jsonSerialize.
	 */
	public function testJsonSerialize() {
		$this->response->set_request_params(
			array(
				'name'  => 'John Doe',
				'email' => 'john@example.com',
				'id'    => 1, // Filtered out.
			)
		);
		$this->response->set_fields(
			array(
				'name'  => 'Name',
				'email' => 'Email',
			)
		);
		$this->response->set_groups( array( 'personal' => 'Personal' ) );

		$data = $this->response->jsonSerialize();

		$this->assertArrayHasKey( 'response', $data );
		$this->assertArrayHasKey( 'fields', $data );
		$this->assertArrayHasKey( 'groups', $data );
		$this->assertEquals( array( 'personal' => 'Personal' ), $data['groups'] );
	}

	/**
	 * Test get_response_data with nested arrays to cover the recursive flatten branch.
	 */
	public function testGetResponseDataWithNestedArrays() {
		$this->response->set_request_params(
			array(
				'personal'    => array(
					'name'  => 'John Doe',
					'email' => 'john@example.com',
				),
				'preferences' => array(
					'newsletter' => 'yes',
				),
				'id'          => 1, // This should be filtered out.
			)
		);
		$this->response->set_fields(
			array(
				'personal'    => 'Personal Information',
				'preferences' => 'Preferences',
			)
		);

		$data = $this->response->get_response_data();

		$this->assertArrayHasKey( 'content', $data );
		$this->assertArrayHasKey( 'fields', $data );

		// Check that nested values are accessible via dot notation.
		$this->assertEquals( 'John Doe', $data['content']->get( 'personal.name' ) );
		$this->assertEquals( 'john@example.com', $data['content']->get( 'personal.email' ) );
		$this->assertEquals( 'yes', $data['content']->get( 'preferences.newsletter' ) );

		// Check that fields are flattened.
		$this->assertArrayHasKey( 'personal.name', $data['fields'] );
		$this->assertArrayHasKey( 'personal.email', $data['fields'] );
		$this->assertArrayHasKey( 'preferences.newsletter', $data['fields'] );

		// Check that field labels are mapped correctly.
		$this->assertEquals( 'personal.name', $data['fields']['personal.name'] );
		$this->assertEquals( 'personal.email', $data['fields']['personal.email'] );
		$this->assertEquals( 'preferences.newsletter', $data['fields']['preferences.newsletter'] );
	}
}
