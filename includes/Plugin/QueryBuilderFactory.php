<?php
/**
 * The QueryBuilderFactory class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use wpdb;

/**
 * The QueryBuilderFactory class.
 */
class QueryBuilderFactory {
	/**
	 * The WordPress database object.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The QueryBuilderFactory constructor.
	 *
	 * @param wpdb $wpdb The WordPress database object.
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Create a new instance of the QueryBuilder class.
	 *
	 * @return QueryBuilder The newly created QueryBuilder instance.
	 */
	public function create(): QueryBuilder {
		return new QueryBuilder( $this->wpdb );
	}
}
