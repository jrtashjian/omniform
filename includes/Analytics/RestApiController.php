<?php
/**
 * The RestApiController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

/**
 * The RestApiController class.
 */
class RestApiController extends WP_REST_Controller {

	/**
	 * The AnalyticsManager instance.
	 *
	 * @var AnalyticsManager
	 */
	protected $analytics_manager;

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'omniform/v1';

	/**
	 * The rest base.
	 *
	 * @var string
	 */
	protected $rest_base = 'analytics';

	/**
	 * Constructor.
	 *
	 * @param AnalyticsManager $analytics_manager The AnalyticsManager instance.
	 */
	public function __construct( AnalyticsManager $analytics_manager ) {
		$this->analytics_manager = $analytics_manager;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/overview',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_overview' ),
				'permission_callback' => array( $this, 'get_overview_permissions_check' ),
				'args'                => array(
					'period' => array(
						'description'       => __( 'The time period for analytics data (1d, 7d, or 30d).', 'omniform' ),
						'type'              => 'string',
						'enum'              => array( '1d', '7d', '30d' ),
						'default'           => '7d',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Checks if a given request has access to get the overview.
	 *
	 * @return true|WP_Error True if the request has access, WP_Error object otherwise.
	 */
	public function get_overview_permissions_check() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_view_analytics',
				__( 'Sorry, you are not allowed to view analytics.', 'omniform' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Retrieves the analytics overview.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_overview( \WP_REST_Request $request ) {
		$period = $this->validate_period( $request->get_param( 'period' ) );

		$current_period = $this->get_current_period( $period );
		$cache_key      = 'omniform_analytics_overview_' . $period . '_' . $current_period['start'] . '_' . $current_period['end'];

		$cached_response = get_transient( $cache_key );

		if ( false !== $cached_response ) {
			return rest_ensure_response( $cached_response );
		}

		$response = $this->fetch_overview_data( $current_period, $period );

		set_transient( $cache_key, $response, DAY_IN_SECONDS );

		return rest_ensure_response( $response );
	}

	/**
	 * Validates the period parameter.
	 *
	 * @param string $period The period to validate.
	 *
	 * @return string The validated period.
	 */
	protected function validate_period( $period ) {
		$valid_periods = array( '1d', '7d', '30d' );

		if ( ! in_array( $period, $valid_periods, true ) ) {
			return '7d';
		}

		return $period;
	}

	/**
	 * Fetches the analytics overview data from the database.
	 *
	 * @param array  $current_period The current period dates.
	 * @param string $period        The period (1d, 7d, or 30d).
	 *
	 * @return array The analytics overview data.
	 */
	private function fetch_overview_data( array $current_period, $period ) {
		$previous_period = $this->get_previous_period( $period );

		$current_impressions_total  = $this->analytics_manager->get_impression_count_by_date_range(
			$current_period['start'],
			$current_period['end'],
			false
		);
		$current_impressions_unique = $this->analytics_manager->get_impression_count_by_date_range(
			$current_period['start'],
			$current_period['end'],
			true
		);

		$previous_impressions_total  = $this->analytics_manager->get_impression_count_by_date_range(
			$previous_period['start'],
			$previous_period['end'],
			false
		);
		$previous_impressions_unique = $this->analytics_manager->get_impression_count_by_date_range(
			$previous_period['start'],
			$previous_period['end'],
			true
		);

		$current_submissions_total  = $this->analytics_manager->get_submission_count_by_date_range(
			$current_period['start'],
			$current_period['end'],
			false
		);
		$current_submissions_unique = $this->analytics_manager->get_submission_count_by_date_range(
			$current_period['start'],
			$current_period['end'],
			true
		);

		$previous_submissions_total  = $this->analytics_manager->get_submission_count_by_date_range(
			$previous_period['start'],
			$previous_period['end'],
			false
		);
		$previous_submissions_unique = $this->analytics_manager->get_submission_count_by_date_range(
			$previous_period['start'],
			$previous_period['end'],
			true
		);

		$current_conversion_rate  = $current_impressions_unique > 0
			? ( $current_submissions_unique / $current_impressions_unique )
			: 0;
		$previous_conversion_rate = $previous_impressions_unique > 0
			? ( $previous_submissions_unique / $previous_impressions_unique )
			: 0;

		return array(
			'period'     => $current_period,
			'metrics'    => array(
				'impressions'     => array(
					'total'  => $current_impressions_total,
					'unique' => $current_impressions_unique,
				),
				'submissions'     => array(
					'total'  => $current_submissions_total,
					'unique' => $current_submissions_unique,
				),
				'conversion_rate' => $current_conversion_rate,
			),
			'comparison' => array(
				'impressions'     => array(
					'total'  => $this->calculate_percentage_change( $current_impressions_total, $previous_impressions_total ),
					'unique' => $this->calculate_percentage_change( $current_impressions_unique, $previous_impressions_unique ),
				),
				'submissions'     => array(
					'total'  => $this->calculate_percentage_change( $current_submissions_total, $previous_submissions_total ),
					'unique' => $this->calculate_percentage_change( $current_submissions_unique, $previous_submissions_unique ),
				),
				'conversion_rate' => $this->calculate_percentage_change( $current_conversion_rate, $previous_conversion_rate ),
			),
		);
	}

	/**
	 * Gets the current period based on the specified duration.
	 *
	 * @param string $period The period (1d, 7d, or 30d).
	 *
	 * @return array The period with start and end dates.
	 */
	protected function get_current_period( $period ) {
		$days_map = array(
			'1d'  => 0,
			'7d'  => 6,
			'30d' => 29,
		);

		$days = $days_map[ $period ] ?? 6;

		return array(
			'start' => gmdate( 'Y-m-d', strtotime( '-' . $days . ' days' ) ),
			'end'   => gmdate( 'Y-m-d' ),
		);
	}

	/**
	 * Gets the previous period based on the specified duration.
	 *
	 * @param string $period The period (1d, 7d, or 30d).
	 *
	 * @return array The period with start and end dates.
	 */
	protected function get_previous_period( $period ) {
		$days_map = array(
			'1d'  => 0,
			'7d'  => 6,
			'30d' => 29,
		);

		$days = $days_map[ $period ] ?? 6;

		return array(
			'start' => gmdate( 'Y-m-d', strtotime( '-' . ( $days * 2 + 1 ) . ' days' ) ),
			'end'   => gmdate( 'Y-m-d', strtotime( '-' . ( $days + 1 ) . ' days' ) ),
		);
	}

	/**
	 * Calculates the percentage change between two values.
	 *
	 * @param float $current  The current value.
	 * @param float $previous The previous value.
	 *
	 * @return float The percentage change as a decimal.
	 */
	protected function calculate_percentage_change( $current, $previous ) {
		$current  = (float) $current;
		$previous = (float) $previous;

		if ( 0 === $previous || ! is_numeric( $current ) || ! is_numeric( $previous ) ) {
			return 0;
		}

		$change = ( $current - $previous ) / $previous;

		if ( ! is_numeric( $change ) || ! is_finite( $change ) ) {
			return 0;
		}

		return round( $change, 4 );
	}
}
