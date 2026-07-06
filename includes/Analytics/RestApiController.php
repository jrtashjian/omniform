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
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/top-forms',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_top_forms' ),
				'permission_callback' => array( $this, 'get_overview_permissions_check' ),
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
		$cache_key = 'omniform_analytics_overview';

		/* $cached_response = get_transient( $cache_key ); */
		/**/
		/* if ( false !== $cached_response ) { */
		/* 	return rest_ensure_response( $cached_response ); */
		/* } */

		$response = $this->fetch_overview_data();

		set_transient( $cache_key, $response, HOUR_IN_SECONDS );

		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the top forms by response count.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_top_forms( \WP_REST_Request $request ) {
		$current_period = $this->get_current_period( '7d' );
		$cache_key      = 'omniform_analytics_top_forms_7d_' . $current_period['local']['start'] . '_' . $current_period['local']['end'];

		/* $cached_response = get_transient( $cache_key ); */
		/**/
		/* if ( false !== $cached_response ) { */
		/* 	return rest_ensure_response( $cached_response ); */
		/* } */

		$forms = $this->analytics_manager->get_top_forms_by_response_count(
			$current_period['gmt']['start'],
			$current_period['gmt']['end']
		);

		$response = array(
			'period' => $current_period['local'],
			'forms'  => $forms,
		);

		set_transient( $cache_key, $response, HOUR_IN_SECONDS );

		return rest_ensure_response( $response );
	}

	/**
	 * Fetches the analytics overview data from the database.
	 *
	 * @return array The analytics overview data.
	 */
	private function fetch_overview_data() {
		$ref_timestamp = current_time( 'timestamp' );
		$day_of_week   = (int) date( 'N', $ref_timestamp );

		$today_start_gmt = get_gmt_from_date( date_i18n( 'Y-m-d 00:00:00' ) );
		$today_end_gmt   = current_time( 'mysql', true );

		$yesterday_start_gmt = get_gmt_from_date( date_i18n( 'Y-m-d 00:00:00', strtotime( '-1 day' ) ) );
		$yesterday_end_gmt   = get_gmt_from_date( date_i18n( 'Y-m-d 23:59:59', strtotime( '-1 day' ) ) );

		$this_monday = strtotime( '-' . ( $day_of_week - 1 ) . ' days', $ref_timestamp );
		$last_monday = strtotime( '-' . ( $day_of_week + 6 ) . ' days', $ref_timestamp );
		$last_sunday = strtotime( '-' . ( $day_of_week ) . ' days', $ref_timestamp );

		$week_start_gmt      = get_gmt_from_date( date_i18n( 'Y-m-d 00:00:00', $this_monday ) );
		$last_week_start_gmt = get_gmt_from_date( date_i18n( 'Y-m-d 00:00:00', $last_monday ) );
		$last_week_end_gmt   = get_gmt_from_date( date_i18n( 'Y-m-d 23:59:59', $last_sunday ) );

		$month_start_gmt      = get_gmt_from_date( date_i18n( 'Y-m-01 00:00:00' ) );
		$last_month_start_gmt = get_gmt_from_date( date_i18n( 'Y-m-01 00:00:00', strtotime( 'first day of last month' ) ) );
		$last_month_end_gmt   = get_gmt_from_date( date_i18n( 'Y-m-t 23:59:59', strtotime( 'last day of last month' ) ) );

		$today_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$today_start_gmt,
			$today_end_gmt,
			false
		);

		$yesterday_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$yesterday_start_gmt,
			$yesterday_end_gmt,
			false
		);

		$week_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$week_start_gmt,
			$today_end_gmt,
			false
		);

		$last_week_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$last_week_start_gmt,
			$last_week_end_gmt,
			false
		);

		$month_unique_impressions = $this->analytics_manager->get_impression_count_by_date_range(
			$month_start_gmt,
			$today_end_gmt,
			true
		);

		$month_unique_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$month_start_gmt,
			$today_end_gmt,
			true
		);

		$month_conversion_rate = $month_unique_impressions > 0
			? round( ( $month_unique_submissions / $month_unique_impressions ) * 100, 1 )
			: 0;

		$last_month_unique_impressions = $this->analytics_manager->get_impression_count_by_date_range(
			$last_month_start_gmt,
			$last_month_end_gmt,
			true
		);

		$last_month_unique_submissions = $this->analytics_manager->get_submission_count_by_date_range(
			$last_month_start_gmt,
			$last_month_end_gmt,
			true
		);

		$last_month_conversion_rate = $last_month_unique_impressions > 0
			? round( ( $last_month_unique_submissions / $last_month_unique_impressions ) * 100, 1 )
			: 0;

		$today_diff       = (int) $today_submissions - (int) $yesterday_submissions;
		$week_diff        = (int) $week_submissions - (int) $last_week_submissions;
		$month_rate_diff  = round( $month_conversion_rate - $last_month_conversion_rate, 1 );

		return array(
			'metrics' => array(
				array(
					'title'    => __( 'Submissions Today', 'omniform' ),
					'value'    => (int) $today_submissions,
					'format'   => 'number',
					'sub_text' => $this->format_comparison_text(
						$today_diff,
						__( 'vs yesterday', 'omniform' )
					),
					'trend'    => $this->calculate_trend( $today_submissions, $yesterday_submissions ),
				),
				array(
					'title'    => __( 'Responses This Week', 'omniform' ),
					'value'    => (int) $week_submissions,
					'format'   => 'number',
					'sub_text' => $this->format_comparison_text(
						$week_diff,
						__( 'vs last week', 'omniform' )
					),
					'trend'    => $this->calculate_trend( $week_submissions, $last_week_submissions ),
				),
				array(
					'title'    => __( 'Average Completion', 'omniform' ),
					'value'    => (float) $month_conversion_rate,
					'format'   => 'percentage',
					'sub_text' => $this->format_comparison_text(
						$month_rate_diff,
						__( 'vs last month', 'omniform' ),
						true
					),
					'trend'    => $this->calculate_trend( $month_conversion_rate, $last_month_conversion_rate ),
				),
			),
		);
	}

	/**
	 * Formats the comparison text for a metric.
	 *
	 * @param float  $diff          The difference between current and previous values.
	 * @param string $label         The comparison period label.
	 * @param bool   $is_percentage Whether the value is a percentage.
	 *
	 * @return string The formatted comparison text.
	 */
	private function format_comparison_text( $diff, $label, $is_percentage = false ) {
		if ( $diff > 0 ) {
			if ( $is_percentage ) {
				return '+' . number_format( $diff, 1 ) . '% ' . $label;
			}

			return '+' . (int) $diff . ' ' . $label;
		}

		if ( $diff < 0 ) {
			if ( $is_percentage ) {
				return '-' . number_format( abs( $diff ), 1 ) . '% ' . $label;
			}

			return '-' . (int) abs( $diff ) . ' ' . $label;
		}

		/* translators: %s: comparison period label */
		return sprintf( __( 'No change %s', 'omniform' ), $label );
	}

	/**
	 * Calculates the trend direction between two values.
	 *
	 * @param float $current  The current value.
	 * @param float $previous The previous value.
	 *
	 * @return string The trend direction ('up', 'down', or 'same').
	 */
	private function calculate_trend( $current, $previous ) {
		if ( $current > $previous ) {
			return 'up';
		}

		if ( $current < $previous ) {
			return 'down';
		}

		return 'same';
	}

	/**
	 * Gets the current period based on the specified duration.
	 *
	 * @param string $period The period (1d, 7d, or 30d).
	 *
	 * @return array The period with start and end dates in local timezone and UTC.
	 */
	protected function get_current_period( $period ) {
		$days_map = array(
			'1d'  => 0,
			'7d'  => 6,
			'30d' => 29,
		);

		$days = $days_map[ $period ] ?? 6;

		// If days is 0, we want today only, so no subtraction.
		$timestamp = ( 0 === $days ) ? false : strtotime( '-' . $days . ' days' );

		$local_start = date_i18n( 'Y-m-d 00:00:00', $timestamp );
		$local_end   = date_i18n( 'Y-m-d 23:59:59' );

		return array(
			'local' => array(
				'start' => $local_start,
				'end'   => $local_end,
			),
			'gmt'   => array(
				'start' => get_gmt_from_date( $local_start ),
				'end'   => get_gmt_from_date( $local_end ),
			),
		);
	}

	/**
	 * Gets the previous period based on the specified duration.
	 *
	 * @param string $period The period (1d, 7d, or 30d).
	 *
	 * @return array The period with start and end dates in UTC for queries.
	 */
	protected function get_previous_period( $period ) {
		$days_map = array(
			'1d'  => 0,
			'7d'  => 6,
			'30d' => 29,
		);

		$days = $days_map[ $period ] ?? 6;

		$local_start = date_i18n( 'Y-m-d 00:00:00', strtotime( '-' . ( $days * 2 + 1 ) . ' days' ) );
		$local_end   = date_i18n( 'Y-m-d 23:59:59', strtotime( '-' . ( $days + 1 ) . ' days' ) );

		return array(
			'start' => get_gmt_from_date( $local_start ),
			'end'   => get_gmt_from_date( $local_end ),
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
		if ( 0 === $previous || ! is_numeric( $current ) || ! is_numeric( $previous ) ) {
			return 0;
		}

		$current  = (float) $current;
		$previous = (float) $previous;

		$change = ( $current - $previous ) / $previous;

		if ( ! is_numeric( $change ) || ! is_finite( $change ) ) {
			return 0;
		}

		return round( $change, 4 );
	}
}
