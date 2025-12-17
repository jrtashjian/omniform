<?php
/**
 * Tests the Response class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Plugin;

use OmniForm\Plugin\Response;

/**
 * Tests the Response class.
 */
class ResponseTest extends \WP_UnitTestCase {
	/**
	 * Test that text_content() properly escapes user input.
	 */
	public function test_text_content_escapes_user_input() {
		$response = new Response();

		// Set up fields with a simple label.
		$response->set_fields( array( 'test_field' => 'Test Label' ) );

		// Set up request params with XSS attempt.
		$response->set_request_params(
			array(
				'test_field' => '<script>alert("XSS")</script>',
			)
		);

		$content = $response->text_content();

		// Verify that the script tags are escaped.
		$this->assertStringNotContainsString( '<script>', $content );
		$this->assertStringNotContainsString( 'alert("XSS")', $content );

		// Verify that escaped content is present.
		$this->assertStringContainsString( '&lt;script&gt;', $content );
		$this->assertStringContainsString( 'alert(&quot;XSS&quot;)', $content );

		// Verify that allowed HTML tags are present.
		$this->assertStringContainsString( '<strong>Test Label:</strong>', $content );
	}

	/**
	 * Test that text_content() handles newlines correctly.
	 */
	public function test_text_content_preserves_newlines() {
		$response = new Response();

		$response->set_fields( array( 'message' => 'Message' ) );

		$response->set_request_params(
			array(
				'message' => "Line 1\nLine 2\nLine 3",
			)
		);

		$content = $response->text_content();

		// Verify that newlines are converted to <br> tags.
		$this->assertStringContainsString( 'Line 1<br />', $content );
		$this->assertStringContainsString( 'Line 2<br />', $content );
		$this->assertStringContainsString( 'Line 3', $content );
	}

	/**
	 * Test that text_content() escapes special characters with newlines.
	 */
	public function test_text_content_escapes_special_chars_with_newlines() {
		$response = new Response();

		$response->set_fields( array( 'comment' => 'Comment' ) );

		$response->set_request_params(
			array(
				'comment' => "<img src=x onerror=\"alert('XSS')\">\nSecond line with <b>bold</b>",
			)
		);

		$content = $response->text_content();

		// Verify that HTML tags are escaped.
		$this->assertStringNotContainsString( '<img', $content );
		$this->assertStringNotContainsString( '<b>bold</b>', $content );
		$this->assertStringNotContainsString( 'onerror=', $content );

		// Verify that escaped content is present.
		$this->assertStringContainsString( '&lt;img', $content );
		$this->assertStringContainsString( '&lt;b&gt;bold&lt;/b&gt;', $content );

		// Verify that newlines are preserved.
		$this->assertStringContainsString( '<br />', $content );
	}

	/**
	 * Test that email_content() strips HTML from user input.
	 */
	public function test_email_content_strips_html() {
		$response = new Response();

		$response->set_fields( array( 'test_field' => 'Test Label' ) );
		$response->set_date( '2024-01-01 12:00:00' );

		$response->set_request_params(
			array(
				'test_field'        => '<script>alert("XSS")</script>',
				'_omniform_user_ip' => '127.0.0.1',
				'_wp_http_referer'  => 'http://example.com',
			)
		);

		$content = $response->email_content();

		// Verify that HTML is stripped for email.
		$this->assertStringNotContainsString( '<script>', $content );
		$this->assertStringNotContainsString( 'alert("XSS")', $content );
	}
}
