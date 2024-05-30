<?php
/**
 * The AnalyticsManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use OmniForm\Plugin\QueryBuilder;

/**
 * The AnalyticsManager class.
 */
class AnalyticsManager {
	/**
	 * The QueryBuilder instance.
	 *
	 * @var QueryBuilder
	 */
	protected $query_builder;

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
	 * @param string       $daily_salt The daily salt.
	 */
	public function __construct( QueryBuilder $query_builder, string $daily_salt ) {
		$this->query_builder = $query_builder;
		$this->daily_salt    = $daily_salt;
	}
}
