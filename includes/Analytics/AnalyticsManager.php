<?php
/**
 * The AnalyticsManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use OmniForm\Plugin\Http\Request;
use OmniForm\Plugin\QueryBuilder;

/**
 * The AnalyticsManager class.
 */
class AnalyticsManager {
	/**
	 * The events table.
	 *
	 * @var string
	 */
	const EVENTS_TABLE = 'omniform_stats_events';

	/**
	 * The visitors table.
	 *
	 * @var string
	 */
	const VISITOR_TABLE = 'omniform_stats_visitors';

	/**
	 * The QueryBuilder instance.
	 *
	 * @var QueryBuilder
	 */
	protected $query_builder;

	/**
	 * The Request instance.
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * The daily salt.
	 *
	 * @var string
	 */
	protected $daily_salt;

	/**
	 * The AnalyticsManager constructor.
	 *
	 * @param QueryBuilder $query_builder The QueryBuilder instance.
	 * @param Request      $request The Request instance.
	 * @param string       $daily_salt The daily salt.
	 */
	public function __construct( QueryBuilder $query_builder, Request $request, string $daily_salt ) {
		$this->query_builder = $query_builder;
		$this->request       = $request;
		$this->daily_salt    = $daily_salt;
	}

	/**
	 * Get the visitor hash.
	 *
	 * @return string The visitor hash.
	 */
	protected function get_visitor_hash() {
		return hash( 'sha256', $this->daily_salt . $this->request->get_ip_address() . $this->request->get_user_agent() );
	}

	/**
	 * Record an event.
	 *
	 * @param int $form_id The form ID.
	 * @param int $event_type The event type.
	 */
	protected function record_event( int $form_id, int $event_type ) {
		$visitor_id = $this->get_visitor_id();

		$this->query_builder->table( self::EVENTS_TABLE )
			->insert(
				array(
					'form_id'    => $form_id,
					'event_type' => $event_type,
					'visitor_id' => $visitor_id,
					'event_time' => current_time( 'mysql', true ),
				)
			);
	}

	/**
	 * Get the visitor ID from the visitor hash.
	 *
	 * @return int The visitor ID.
	 */
	protected function get_visitor_id() {
		$cache_key = 'omniform_visitor_id_' . $this->get_visitor_hash();
		$cached_id = wp_cache_get( $cache_key );

		if ( false !== $cached_id ) {
			return $cached_id;
		}

		$visitor_results = $this->query_builder->table( self::VISITOR_TABLE )
			->select( 'visitor_id' )
			->where( 'visitor_hash', '=', $this->get_visitor_hash() )
			->get();

		if ( empty( $visitor_results ) ) {
			$this->query_builder->table( self::VISITOR_TABLE )
				->insert(
					array(
						'visitor_hash' => $this->get_visitor_hash(),
					)
				);

			$visitor_id = $this->query_builder->get_last_insert_id();
			wp_cache_set( $cache_key, $visitor_id );
			return $visitor_id;
		}

		$visitor_id = $visitor_results[0]->visitor_id;
		wp_cache_set( $cache_key, $visitor_id );
		return $visitor_id;
	}

	/**
	 * Record an impression.
	 *
	 * @param int $form_id The form ID.
	 */
	public function record_impression( int $form_id ) {
		$this->record_event( $form_id, EventType::IMPRESSION );
	}

	/**
	 * Record a successful submission.
	 *
	 * @param int $form_id The form ID.
	 */
	public function record_submission_success( int $form_id ) {
		$this->record_event( $form_id, EventType::SUBMISSION_SUCCESS );
	}

	/**
	 * Record a failed submission.
	 *
	 * @param int $form_id The form ID.
	 */
	public function record_submission_failure( int $form_id ) {
		$this->record_event( $form_id, EventType::SUBMISSION_FAILURE );
	}

	/**
	 * Get the impression count.
	 *
	 * @param int  $form_id The form ID.
	 * @param bool $unique Whether to count unique impressions.
	 *
	 * @return int The impression count.
	 */
	public function get_impression_count( int $form_id, bool $unique = false ) {
		return $this->query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->where( 'event_type', '=', EventType::IMPRESSION )
			->count( $unique ? 'DISTINCT visitor_id' : 'event_id' );
	}

	/**
	 * Get the submission count.
	 *
	 * @param int  $form_id The form ID.
	 * @param bool $unique Whether to count unique submissions.
	 *
	 * @return int The submission count.
	 */
	public function get_submission_count( int $form_id, bool $unique = false ) {
		return $this->query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->where( 'event_type', '=', EventType::SUBMISSION_SUCCESS )
			->count( $unique ? 'DISTINCT visitor_id' : 'event_id' );
	}

	/**
	 * Get the failed submission count.
	 *
	 * @param int  $form_id The form ID.
	 * @param bool $unique Whether to count unique failed submissions.
	 *
	 * @return int The failed submission count.
	 */
	public function get_failed_submission_count( int $form_id, bool $unique = false ) {
		return $this->query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->where( 'event_type', '=', EventType::SUBMISSION_FAILURE )
			->count( $unique ? 'DISTINCT visitor_id' : 'event_id' );
	}

	/**
	 * Get the conversion rate.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return float The conversion rate.
	 */
	public function get_conversion_rate( int $form_id ) {
		$impressions = $this->get_impression_count( $form_id, true );
		$submissions = $this->get_submission_count( $form_id, true );

		return $impressions > 0 ? ( $submissions / $impressions ) : 0;
	}

	/**
	 * Get the count of recent submissions (success or failure) by the current visitor for a specific form within a time window.
	 *
	 * @param int $form_id The form ID.
	 * @param int $seconds The time window in seconds (default: 3600 for 1 hour).
	 *
	 * @return int The count of recent submissions.
	 */
	public function get_recent_submissions_count( int $form_id, int $seconds = 3600 ) {
		$visitor_id     = $this->get_visitor_id();
		$time_threshold = date_i18n( 'Y-m-d H:i:s', time() - $seconds );

		return (int) $this->query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->where( 'visitor_id', '=', $visitor_id )
			->where( 'event_time', '>=', $time_threshold )
			->where( 'event_type', 'IN', array( EventType::SUBMISSION_SUCCESS, EventType::SUBMISSION_FAILURE ) )
			->count( 'event_id' );
	}

	/**
	 * Get the impression count by date range.
	 *
	 * @param string $start_date The start date.
	 * @param string $end_date   The end date.
	 * @param bool   $unique     Whether to count unique impressions.
	 *
	 * @return int The impression count.
	 */
	public function get_impression_count_by_date_range( string $start_date, string $end_date, bool $unique = false ) {
		return $this->get_event_count_by_date_range( EventType::IMPRESSION, $start_date, $end_date, $unique );
	}

	/**
	 * Get the submission count by date range.
	 *
	 * @param string $start_date The start date.
	 * @param string $end_date   The end date.
	 * @param bool   $unique     Whether to count unique submissions.
	 *
	 * @return int The submission count.
	 */
	public function get_submission_count_by_date_range( string $start_date, string $end_date, bool $unique = false ) {
		return $this->get_event_count_by_date_range( EventType::SUBMISSION_SUCCESS, $start_date, $end_date, $unique );
	}

	/**
	 * Get the event count by date range.
	 *
	 * @param int    $event_type The event type.
	 * @param string $start_date The start date.
	 * @param string $end_date   The end date.
	 * @param bool   $unique     Whether to count unique events.
	 *
	 * @return int The event count.
	 */
	private function get_event_count_by_date_range( int $event_type, string $start_date, string $end_date, bool $unique ) {
		return $this->query_builder->table( self::EVENTS_TABLE )
			->where( 'event_type', '=', $event_type )
			->where( 'event_time', '>=', $start_date )
			->where( 'event_time', '<=', $end_date )
			->count( $unique ? 'DISTINCT visitor_id' : 'event_id' );
	}

	/**
	 * Get the top 5 forms by response count with metrics.
	 *
	 * @param string $start_date The start date.
	 * @param string $end_date   The end date.
	 *
	 * @return array The top 5 forms with metrics.
	 */
	public function get_top_forms_by_response_count( string $start_date, string $end_date ) {
		$results = $this->query_builder->table( self::EVENTS_TABLE )
			->select(
				array(
					'form_id as id',
					$this->query_builder->prefix_table( 'posts' ) . '.post_title AS title',
					'COUNT(CASE WHEN event_type = ' . EventType::IMPRESSION . ' THEN event_id END) AS total_impressions',
					'COUNT(DISTINCT CASE WHEN event_type = ' . EventType::IMPRESSION . ' THEN visitor_id END) AS unique_impressions',
					'COUNT(CASE WHEN event_type = ' . EventType::SUBMISSION_SUCCESS . ' THEN event_id END) AS response_count',
					'COUNT(DISTINCT CASE WHEN event_type = ' . EventType::SUBMISSION_SUCCESS . ' THEN visitor_id END) AS unique_responses',
				)
			)
			->join( 'posts', $this->query_builder->prefix_table( 'posts' ) . '.ID = ' . $this->query_builder->prefix_table( self::EVENTS_TABLE ) . '.form_id', 'LEFT' )
			->where( 'event_time', '>=', $start_date )
			->where( 'event_time', '<=', $end_date )
			->where( 'event_type', 'IN', array( EventType::IMPRESSION, EventType::SUBMISSION_SUCCESS ) )
			->group_by( 'form_id' )
			->group_by( 'wp_posts.post_title' )
			->order_by( 'response_count', 'DESC' )
			->limit( 5 )
			->get();

		foreach ( $results as $row ) {
			$row->conversion_rate = $row->unique_impressions > 0
				? round( $row->unique_responses / $row->unique_impressions, 4 )
				: 0;
		}

		return $results;
	}

	/**
	 * Purge data for a form.
	 *
	 * @param int $form_id The form ID.
	 */
	public function delete_form_data( int $form_id ) {
		$this->query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->delete();
	}
}
