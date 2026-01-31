<?php
/**
 * Tests the AnalyticsManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Analytics;

use OmniForm\Analytics\AnalyticsManager;
use OmniForm\Analytics\EventType;
use OmniForm\Plugin\QueryBuilder;
use OmniForm\Plugin\Http\Request;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;
use ReflectionClass;

/**
 * Tests the AnalyticsManager class.
 */
class AnalyticsManagerTest extends BaseTestCase {
	/**
	 * The QueryBuilder mock.
	 *
	 * @var \Mockery\MockInterface|QueryBuilder
	 */
	private $query_builder;

	/**
	 * The Request mock.
	 *
	 * @var \Mockery\MockInterface|Request
	 */
	private $request;

	/**
	 * The AnalyticsManager instance.
	 *
	 * @var AnalyticsManager
	 */
	private $analytics_manager;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->query_builder = Mockery::mock( QueryBuilder::class );
		$this->request       = Mockery::mock( Request::class );

		$this->request->shouldReceive( 'get_ip_address' )->andReturn( '192.168.1.1' );
		$this->request->shouldReceive( 'get_user_agent' )->andReturn( 'Mozilla/5.0' );

		$this->analytics_manager = new AnalyticsManager( $this->query_builder, $this->request, 'test_salt' );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Helper method to invoke the protected get_visitor_id method.
	 *
	 * @return int The visitor ID.
	 */
	private function invokeGetVisitorId() {
		$reflection = new ReflectionClass( $this->analytics_manager );
		$method     = $reflection->getMethod( 'get_visitor_id' );
		$method->setAccessible( true );
		return $method->invoke( $this->analytics_manager );
	}

	/**
	 * Test get_visitor_id returns cached visitor ID.
	 */
	public function testGetVisitorIdReturnsCachedValue() {
		WP_Mock::userFunction( 'wp_cache_get' )
			->once()
			->with( \WP_Mock\Functions::type( 'string' ) )
			->andReturn( 789 );

		$visitor_id = $this->invokeGetVisitorId();

		$this->assertEquals( 789, $visitor_id );
	}

	/**
	 * Test get_visitor_id creates new visitor when not cached and not in database.
	 */
	public function testGetVisitorIdCreatesNewVisitor() {
		WP_Mock::userFunction( 'wp_cache_get' )
			->once()
			->andReturn( false );

		$this->query_builder->shouldReceive( 'table' )
			->once()
			->with( AnalyticsManager::VISITOR_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'select' )
			->once()
			->with( 'visitor_id' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->once()
			->with( 'visitor_hash', '=', \Mockery::type( 'string' ) )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'get' )
			->once()
			->andReturn( array() );

		$this->query_builder->shouldReceive( 'table' )
			->once()
			->with( AnalyticsManager::VISITOR_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'insert' )
			->once()
			->with(
				\Mockery::on(
					function ( $arg ) {
						return isset( $arg['visitor_hash'] ) && is_string( $arg['visitor_hash'] );
					}
				)
			);
		$this->query_builder->shouldReceive( 'get_last_insert_id' )
			->once()
			->andReturn( 456 );

		WP_Mock::userFunction( 'wp_cache_set' )
			->once()
			->with( \WP_Mock\Functions::type( 'string' ), 456 );

		$visitor_id = $this->invokeGetVisitorId();

		$this->assertEquals( 456, $visitor_id );
	}

	/**
	 * Test get_visitor_id returns existing visitor from database.
	 */
	public function testGetVisitorIdReturnsExistingVisitor() {
		WP_Mock::userFunction( 'wp_cache_get' )
			->once()
			->andReturn( false );

		$visitor_result             = new \stdClass();
		$visitor_result->visitor_id = 123;

		$this->query_builder->shouldReceive( 'table' )
			->once()
			->with( AnalyticsManager::VISITOR_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'select' )
			->once()
			->with( 'visitor_id' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->once()
			->with( 'visitor_hash', '=', \Mockery::type( 'string' ) )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'get' )
			->once()
			->andReturn( array( $visitor_result ) );

		WP_Mock::userFunction( 'wp_cache_set' )
			->once()
			->with( \WP_Mock\Functions::type( 'string' ), 123 );

		$visitor_id = $this->invokeGetVisitorId();

		$this->assertEquals( 123, $visitor_id );
	}

	/**
	 * Test record_impression uses cached visitor ID.
	 */
	public function testRecordImpressionUsesCachedVisitor() {
		WP_Mock::userFunction( 'wp_cache_get' )->andReturn( 123 );
		WP_Mock::userFunction( 'current_time' )->with( 'mysql' )->andReturn( '2023-01-01 12:00:00' );

		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'insert' )
			->with(
				array(
					'form_id'    => 1,
					'event_type' => EventType::IMPRESSION,
					'visitor_id' => 123,
					'event_time' => '2023-01-01 12:00:00',
				)
			);

		$this->analytics_manager->record_impression( 1 );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test record_submission_success records event with correct event type.
	 */
	public function testRecordSubmissionSuccess() {
		WP_Mock::userFunction( 'wp_cache_get' )->andReturn( 123 );
		WP_Mock::userFunction( 'current_time' )->with( 'mysql' )->andReturn( '2023-01-01 12:00:00' );

		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'insert' )
			->with(
				array(
					'form_id'    => 1,
					'event_type' => EventType::SUBMISSION_SUCCESS,
					'visitor_id' => 123,
					'event_time' => '2023-01-01 12:00:00',
				)
			);

		$this->analytics_manager->record_submission_success( 1 );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test record_submission_failure records event with correct event type.
	 */
	public function testRecordSubmissionFailure() {
		WP_Mock::userFunction( 'wp_cache_get' )->andReturn( 123 );
		WP_Mock::userFunction( 'current_time' )->with( 'mysql' )->andReturn( '2023-01-01 12:00:00' );

		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'insert' )
			->with(
				array(
					'form_id'    => 1,
					'event_type' => EventType::SUBMISSION_FAILURE,
					'visitor_id' => 123,
					'event_time' => '2023-01-01 12:00:00',
				)
			);

		$this->analytics_manager->record_submission_failure( 1 );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test get_impression_count with unique parameter.
	 */
	public function testGetImpressionCountUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'DISTINCT visitor_id' )
			->andReturn( 42 );

		$result = $this->analytics_manager->get_impression_count( 1, true );

		$this->assertEquals( 42, $result );
	}

	/**
	 * Test get_impression_count without unique parameter.
	 */
	public function testGetImpressionCountNonUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'event_id' )
			->andReturn( 100 );

		$result = $this->analytics_manager->get_impression_count( 1, false );

		$this->assertEquals( 100, $result );
	}

	/**
	 * Test get_submission_count with unique parameter.
	 */
	public function testGetSubmissionCountUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'DISTINCT visitor_id' )
			->andReturn( 10 );

		$result = $this->analytics_manager->get_submission_count( 1, true );

		$this->assertEquals( 10, $result );
	}

	/**
	 * Test get_failed_submission_count with unique parameter.
	 */
	public function testGetFailedSubmissionCountUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_FAILURE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'DISTINCT visitor_id' )
			->andReturn( 5 );

		$result = $this->analytics_manager->get_failed_submission_count( 1, true );

		$this->assertEquals( 5, $result );
	}

	/**
	 * Test get_conversion_rate calculates ratio correctly.
	 */
	public function testGetConversionRate() {
		// First call for impressions, second for submissions.
		$this->query_builder->shouldReceive( 'table' )
			->twice()
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->twice()
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->twice()
			->with( 'DISTINCT visitor_id' )
			->andReturn( 100, 10 );

		$result = $this->analytics_manager->get_conversion_rate( 1 );

		$this->assertEquals( 0.1, $result );
	}

	/**
	 * Test get_conversion_rate returns zero when no impressions.
	 */
	public function testGetConversionRateWithZeroImpressions() {
		$this->query_builder->shouldReceive( 'table' )
			->twice()
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->twice()
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->twice()
			->with( 'DISTINCT visitor_id' )
			->andReturn( 0, 10 );

		$result = $this->analytics_manager->get_conversion_rate( 1 );

		$this->assertEquals( 0, $result );
	}

	/**
	 * Test get_recent_submissions_count queries with correct time threshold.
	 */
	public function testGetRecentSubmissionsCount() {
		WP_Mock::userFunction( 'wp_cache_get' )->andReturn( 123 );
		WP_Mock::userFunction( 'date_i18n' )
			->with( 'Y-m-d H:i:s', \WP_Mock\Functions::type( 'int' ) )
			->andReturn( '2023-01-01 11:00:00' );

		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'visitor_id', '=', 123 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '>=', '2023-01-01 11:00:00' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', 'IN', array( EventType::SUBMISSION_SUCCESS, EventType::SUBMISSION_FAILURE ) )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'event_id' )
			->andReturn( 5 );

		$result = $this->analytics_manager->get_recent_submissions_count( 1, 3600 );

		$this->assertEquals( 5, $result );
	}

	/**
	 * Test delete_form_data deletes events for specified form.
	 */
	public function testDeleteFormData() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'form_id', '=', 1 )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'delete' );

		$this->analytics_manager->delete_form_data( 1 );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test get_impression_count_by_date_range with unique parameter.
	 */
	public function testGetImpressionCountByDateRangeUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '>=', '2023-01-01' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '<=', '2023-01-07' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'DISTINCT visitor_id' )
			->andReturn( 50 );

		$result = $this->analytics_manager->get_impression_count_by_date_range( '2023-01-01', '2023-01-07', true );

		$this->assertEquals( 50, $result );
	}

	/**
	 * Test get_impression_count_by_date_range without unique parameter.
	 */
	public function testGetImpressionCountByDateRangeNonUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::IMPRESSION )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '>=', '2023-01-01' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '<=', '2023-01-07' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'event_id' )
			->andReturn( 75 );

		$result = $this->analytics_manager->get_impression_count_by_date_range( '2023-01-01', '2023-01-07', false );

		$this->assertEquals( 75, $result );
	}

	/**
	 * Test get_submission_count_by_date_range with unique parameter.
	 */
	public function testGetSubmissionCountByDateRangeUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '>=', '2023-01-01' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '<=', '2023-01-07' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'DISTINCT visitor_id' )
			->andReturn( 15 );

		$result = $this->analytics_manager->get_submission_count_by_date_range( '2023-01-01', '2023-01-07', true );

		$this->assertEquals( 15, $result );
	}

	/**
	 * Test get_submission_count_by_date_range without unique parameter.
	 */
	public function testGetSubmissionCountByDateRangeNonUnique() {
		$this->query_builder->shouldReceive( 'table' )
			->with( AnalyticsManager::EVENTS_TABLE )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '>=', '2023-01-01' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'where' )
			->with( 'event_time', '<=', '2023-01-07' )
			->andReturnSelf();
		$this->query_builder->shouldReceive( 'count' )
			->with( 'event_id' )
			->andReturn( 20 );

		$result = $this->analytics_manager->get_submission_count_by_date_range( '2023-01-01', '2023-01-07', false );

		$this->assertEquals( 20, $result );
	}
}
