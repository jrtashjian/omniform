<?php
/**
 * Tests the FormsController rate limiting.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Plugin\Api;

use OmniForm\Analytics\AnalyticsManager;
use OmniForm\Plugin\Api\FormsController;

/**
 * Tests the FormsController rate limiting.
 */
class FormsControllerTest extends \WP_UnitTestCase {
	/**
	 * The FormsController instance.
	 *
	 * @var FormsController
	 */
	protected $controller;

	/**
	 * Test form ID.
	 *
	 * @var int
	 */
	protected $form_id;

	/**
	 * This method is called before each test.
	 */
	public function set_up() {
		parent::set_up();

		$this->controller = new FormsController( 'omniform' );
		$this->controller->register_routes();

		// Create a test form
		$this->form_id = $this->factory->post->create(
			array(
				'post_type'    => 'omniform',
				'post_status'  => 'publish',
				'post_content' => '<!-- wp:omniform/form --><!-- /wp:omniform/form -->',
			)
		);
	}

	/**
	 * Test rate limiting blocks excessive submissions.
	 */
	public function test_rate_limiting_blocks_excessive_submissions() {
		$analytics_manager = omniform()->get( AnalyticsManager::class );

		// Simulate 10 recent submissions (the limit)
		for ( $i = 0; $i < 10; $i++ ) {
			$analytics_manager->record_submission_success( $this->form_id );
		}

		// Create a request
		$request = new \WP_REST_Request( 'POST', '/wp/v2/omniform/' . $this->form_id . '/responses' );
		$request->set_param( 'id', $this->form_id );

		// Attempt to submit - should be rate limited
		$response = $this->controller->create_response( $request );

		$this->assertInstanceOf( \WP_Error::class, $response );
		$this->assertEquals( 'rate_limit_exceeded', $response->get_error_code() );
		$this->assertEquals( 429, $response->get_error_data()['status'] );
	}

	/**
	 * Test rate limiting allows submissions under the limit.
	 */
	public function test_rate_limiting_allows_submissions_under_limit() {
		$analytics_manager = omniform()->get( AnalyticsManager::class );

		// Simulate 5 recent submissions (under the limit of 10)
		for ( $i = 0; $i < 5; $i++ ) {
			$analytics_manager->record_submission_success( $this->form_id );
		}

		// Create a request
		$request = new \WP_REST_Request( 'POST', '/wp/v2/omniform/' . $this->form_id . '/responses' );
		$request->set_param( 'id', $this->form_id );

		// Attempt to submit - should NOT be rate limited but may fail validation
		$response = $this->controller->create_response( $request );

		// Should not be a rate limit error
		if ( $response instanceof \WP_Error ) {
			$this->assertNotEquals( 'rate_limit_exceeded', $response->get_error_code() );
		}
	}

	/**
	 * Test rate limiting counts both successful and failed submissions.
	 */
	public function test_rate_limiting_counts_failed_submissions() {
		$analytics_manager = omniform()->get( AnalyticsManager::class );

		// Simulate 10 failed submissions
		for ( $i = 0; $i < 10; $i++ ) {
			$analytics_manager->record_submission_failure( $this->form_id );
		}

		// Create a request
		$request = new \WP_REST_Request( 'POST', '/wp/v2/omniform/' . $this->form_id . '/responses' );
		$request->set_param( 'id', $this->form_id );

		// Attempt to submit - should be rate limited
		$response = $this->controller->create_response( $request );

		$this->assertInstanceOf( \WP_Error::class, $response );
		$this->assertEquals( 'rate_limit_exceeded', $response->get_error_code() );
	}

	/**
	 * Test rate limiting returns proper error message.
	 */
	public function test_rate_limiting_error_message() {
		$analytics_manager = omniform()->get( AnalyticsManager::class );

		// Simulate 10 recent submissions
		for ( $i = 0; $i < 10; $i++ ) {
			$analytics_manager->record_submission_success( $this->form_id );
		}

		// Create a request
		$request = new \WP_REST_Request( 'POST', '/wp/v2/omniform/' . $this->form_id . '/responses' );
		$request->set_param( 'id', $this->form_id );

		// Attempt to submit
		$response = $this->controller->create_response( $request );

		$this->assertInstanceOf( \WP_Error::class, $response );
		$this->assertStringContainsString( 'Too many form submissions', $response->get_error_message() );
	}
}
