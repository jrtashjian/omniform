<?php
/**
 * Tests the AnalyticsManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Analytics;

use OmniForm\Analytics\AnalyticsManager;
use OmniForm\Analytics\EventType;
use OmniForm\Plugin\QueryBuilderFactory;

/**
 * Tests the AnalyticsManager class.
 */
class AnalyticsManagerTest extends \WP_UnitTestCase {
	/**
	 * The AnalyticsManager instance.
	 *
	 * @var AnalyticsManager
	 */
	protected $analytics_manager;

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

		$this->analytics_manager = omniform()->get( AnalyticsManager::class );
		$this->form_id           = $this->factory->post->create(
			array(
				'post_type'   => 'omniform',
				'post_status' => 'publish',
			)
		);
	}

	/**
	 * Test get_recent_submissions_count returns 0 for no submissions.
	 */
	public function test_get_recent_submissions_count_returns_zero_for_no_submissions() {
		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id );
		$this->assertEquals( 0, $count );
	}

	/**
	 * Test get_recent_submissions_count counts successful submissions.
	 */
	public function test_get_recent_submissions_count_counts_successful_submissions() {
		// Record successful submissions
		$this->analytics_manager->record_submission_success( $this->form_id );
		$this->analytics_manager->record_submission_success( $this->form_id );

		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id );
		$this->assertEquals( 2, $count );
	}

	/**
	 * Test get_recent_submissions_count counts failed submissions.
	 */
	public function test_get_recent_submissions_count_counts_failed_submissions() {
		// Record failed submissions
		$this->analytics_manager->record_submission_failure( $this->form_id );
		$this->analytics_manager->record_submission_failure( $this->form_id );
		$this->analytics_manager->record_submission_failure( $this->form_id );

		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id );
		$this->assertEquals( 3, $count );
	}

	/**
	 * Test get_recent_submissions_count counts both successful and failed submissions.
	 */
	public function test_get_recent_submissions_count_counts_both_success_and_failure() {
		// Record mixed submissions
		$this->analytics_manager->record_submission_success( $this->form_id );
		$this->analytics_manager->record_submission_failure( $this->form_id );
		$this->analytics_manager->record_submission_success( $this->form_id );
		$this->analytics_manager->record_submission_failure( $this->form_id );

		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id );
		$this->assertEquals( 4, $count );
	}

	/**
	 * Test get_recent_submissions_count respects time window.
	 */
	public function test_get_recent_submissions_count_respects_time_window() {
		// Record a submission
		$this->analytics_manager->record_submission_success( $this->form_id );

		// Check count with 1 hour window
		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id, 3600 );
		$this->assertEquals( 1, $count );

		// Manually insert an old event (older than the time window)
		global $wpdb;
		$visitor_id     = $this->get_property_value( $this->analytics_manager, 'get_visitor_id' );
		$table          = $wpdb->prefix . AnalyticsManager::EVENTS_TABLE;
		$old_event_time = gmdate( 'Y-m-d H:i:s', time() - 7200 ); // 2 hours ago

		$wpdb->insert(
			$table,
			array(
				'form_id'    => $this->form_id,
				'visitor_id' => $visitor_id,
				'event_type' => EventType::SUBMISSION_SUCCESS,
				'event_time' => $old_event_time,
			)
		);

		// Should still only count the recent one within 1 hour
		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id, 3600 );
		$this->assertEquals( 1, $count );

		// With 3 hour window, should count both
		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id, 10800 );
		$this->assertEquals( 2, $count );
	}

	/**
	 * Test get_recent_submissions_count does not count impressions.
	 */
	public function test_get_recent_submissions_count_does_not_count_impressions() {
		// Record impressions and submissions
		$this->analytics_manager->record_impression( $this->form_id );
		$this->analytics_manager->record_impression( $this->form_id );
		$this->analytics_manager->record_submission_success( $this->form_id );

		$count = $this->analytics_manager->get_recent_submissions_count( $this->form_id );
		$this->assertEquals( 1, $count );
	}

	/**
	 * Helper method to call protected/private methods.
	 *
	 * @param object $object Object instance.
	 * @param string $method Method name to call.
	 *
	 * @return mixed
	 */
	protected function get_property_value( $object, $method ) {
		$reflection = new \ReflectionClass( $object );
		$method     = $reflection->getMethod( $method );
		$method->setAccessible( true );
		return $method->invoke( $object );
	}
}
