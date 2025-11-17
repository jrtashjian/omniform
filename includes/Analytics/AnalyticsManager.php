<?php
/**
 * The AnalyticsManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use OmniForm\Plugin\QueryBuilderFactory;

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
	protected $query_builder_factory;

	/**
	 * The daily salt.
	 *
	 * @var string
	 */
	protected $daily_salt;

	/**
	 * The AnalyticsManager constructor.
	 *
	 * @param QueryBuilderFactory $query_builder_factory The QueryBuilderFactory instance.
	 * @param string              $daily_salt The daily salt.
	 */
	public function __construct( QueryBuilderFactory $query_builder_factory, string $daily_salt ) {
		$this->query_builder_factory = $query_builder_factory;
		$this->daily_salt            = $daily_salt;
	}

	/**
	 * Get the user agent.
	 *
	 * @return string The user agent.
	 */
	protected function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';
	}

	/**
	 * Get the IP address.
	 *
	 * @return string The IP address.
	 */
	protected function get_ip_address() {
		$ip = filter_var( $_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP );
		return $ip ? $ip : '';
	}

	/**
	 * Get the visitor hash.
	 *
	 * @return string The visitor hash.
	 */
	protected function get_visitor_hash() {
		return hash( 'sha256', $this->daily_salt . $this->get_ip_address() . $this->get_user_agent() );
	}

	/**
	 * Record an event.
	 *
	 * @param int $form_id The form ID.
	 * @param int $event_type The event type.
	 */
	protected function record_event( int $form_id, int $event_type ) {
		$query_builder = $this->query_builder_factory->create();

		$query_builder->table( self::EVENTS_TABLE )
			->insert(
				array(
					'form_id'    => $form_id,
					'event_type' => $event_type,
					'visitor_id' => $this->get_visitor_id(),
					'event_time' => current_time( 'mysql' ),
				)
			);
	}

	/**
	 * Get the visitor ID from the visitor hash.
	 *
	 * @return int The visitor ID.
	 */
	protected function get_visitor_id() {
		$query_builder = $this->query_builder_factory->create();

		$visitor_results = $query_builder->table( self::VISITOR_TABLE )
			->select( 'visitor_id' )
			->where( 'visitor_hash', '=', $this->get_visitor_hash() )
			->get();

		if ( empty( $visitor_results ) ) {
			$query_builder->table( self::VISITOR_TABLE )
				->insert(
					array(
						'visitor_hash' => $this->get_visitor_hash(),
					)
				);

			return $query_builder->get_last_insert_id();
		}

		return $visitor_results[0]->visitor_id;
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
		$query_builder = $this->query_builder_factory->create();

		return $query_builder->table( self::EVENTS_TABLE )
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
		$query_builder = $this->query_builder_factory->create();

		return $query_builder->table( self::EVENTS_TABLE )
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
		$query_builder = $this->query_builder_factory->create();

		return $query_builder->table( self::EVENTS_TABLE )
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
	 * Purge data for a form.
	 *
	 * @param int $form_id The form ID.
	 */
	public function delete_form_data( int $form_id ) {
		$query_builder = $this->query_builder_factory->create();

		$query_builder->table( self::EVENTS_TABLE )
			->where( 'form_id', '=', $form_id )
			->delete();
	}
}
