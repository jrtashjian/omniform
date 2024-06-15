<?php
/**
 * The QueryBuilder class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use wpdb;

/**
 * The QueryBuilder class.
 */
class QueryBuilder {
	/**
	 * The WordPress database class.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The columns to select.
	 *
	 * @var array
	 */
	protected $selects = array();

	/**
	 * The table to query from.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The where clauses.
	 *
	 * @var array
	 */
	protected $wheres = array();

	/**
	 * The order by clauses.
	 *
	 * @var array
	 */
	protected $order_bys = array();

	/**
	 * The group by clauses.
	 *
	 * @var array
	 */
	protected $group_bys = array();

	/**
	 * The limit.
	 *
	 * @var int
	 */
	protected $limit;

	/**
	 * Create a new QueryBuilder instance.
	 *
	 * @param wpdb $wpdb The WordPress database class.
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Set the columns to select.
	 *
	 * @param array|string $columns The columns to select.
	 *
	 * @return QueryBuilder
	 */
	public function select( $columns ) {
		$this->selects = is_array( $columns ) ? $columns : func_get_args();

		return $this;
	}

	/**
	 * Set the table to query from.
	 *
	 * @param string $table The table to query from.
	 *
	 * @return QueryBuilder
	 */
	public function table( $table ) {
		$this->table = $table;

		return $this;
	}

	/**
	 * Add a where clause to the query.
	 *
	 * @param string $column The column to filter by.
	 * @param string $operator The operator to use.
	 * @param mixed  $value The value to compare against.
	 * @param string $boolean The boolean operator to use.
	 *
	 * @return QueryBuilder
	 */
	public function where( $column, $operator, $value = null, $boolean = 'AND' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$this->wheres[] = compact( 'column', 'operator', 'value', 'boolean' );

		return $this;
	}

	/**
	 * Add an order by clause to the query.
	 *
	 * @param string $column The column to order by.
	 * @param string $direction The direction to order by.
	 *
	 * @return QueryBuilder
	 */
	public function order_by( $column, $direction = 'ASC' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$this->order_bys[] = compact( 'column', 'direction' );

		return $this;
	}

	/**
	 * Add a group by clause to the query.
	 *
	 * @param string $column The column to group by.
	 *
	 * @return QueryBuilder
	 */
	public function group_by( $column ) {
		$this->group_bys[] = $column;

		return $this;
	}

	/**
	 * Set the limit for the query.
	 *
	 * @param int $limit The limit.
	 *
	 * @return QueryBuilder
	 */
	public function limit( $limit ) {
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Execute the query and return the results.
	 *
	 * @return array|object|null The query results.
	 */
	public function get() {
		$query = 'SELECT ' . implode( ', ', $this->selects ) . ' FROM `' . $this->wpdb->prefix . $this->table . '`';

		if ( ! empty( $this->wheres ) ) {
			$query .= ' WHERE ' . $this->build_where_clause();
		}

		if ( ! empty( $this->order_bys ) ) {
			$query .= ' ORDER BY ' . $this->build_order_by_clause();
		}

		if ( ! empty( $this->group_bys ) ) {
			$query .= ' GROUP BY ' . implode( ', ', $this->group_bys );
		}

		if ( ! empty( $this->limit ) ) {
			$query .= ' LIMIT ' . (int) $this->limit;
		}

		return $this->wpdb->get_results( $query ); // phpcs:ignore WordPress.DB -- Query has been prepared.
	}

	/**
	 * Count the number of records returned by the query.
	 *
	 * @param string $select The column to count.
	 *
	 * @return int The number of records.
	 */
	public function count( $select = '*' ) {
		$query = 'SELECT COUNT(' . $select . ') FROM `' . $this->wpdb->prefix . $this->table . '`';

		if ( ! empty( $this->wheres ) ) {
			$query .= ' WHERE ' . $this->build_where_clause();
		}

		if ( ! empty( $this->group_bys ) ) {
			$query .= ' GROUP BY ' . implode( ', ', $this->group_bys );
		}

		return (int) $this->wpdb->get_var( $query ); // phpcs:ignore WordPress.DB -- Query has been prepared.
	}

	/**
	 * Build the WHERE clause.
	 *
	 * @return string
	 */
	protected function build_where_clause() {
		$conditions = array();
		$values     = array();

		foreach ( $this->wheres as $index => $where ) {
			$placeholder  = is_numeric( $where['value'] ) ? '%d' : '%s';
			$conditions[] = ( $index > 0 ? $where['boolean'] . ' ' : '' ) . '`' . $where['column'] . '` ' . $where['operator'] . ' ' . $placeholder;
			$values[]     = $where['value'];
		}

		return $this->wpdb->prepare( implode( ' ', $conditions ), $values ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Build the ORDER BY clause.
	 *
	 * @return string
	 */
	protected function build_order_by_clause() {
		$conditions = array();

		foreach ( $this->order_bys as $order_by ) {
			$conditions[] = '`' . $order_by['column'] . '` ' . $order_by['direction'];
		}

		return implode( ', ', $conditions );
	}

	/**
	 * Insert a new record into the table.
	 *
	 * @param array $data The data to insert.
	 *
	 * @return int|bool The number of rows inserted or false on failure.
	 */
	public function insert( array $data ) {
		return $this->wpdb->insert( $this->wpdb->prefix . $this->table, $data );
	}

	/**
	 * Get the ID of the last inserted record.
	 *
	 * @return int The ID of the last inserted record.
	 */
	public function get_last_insert_id() {
		return $this->wpdb->insert_id;
	}

	/**
	 * Update records in the table.
	 *
	 * @param array $data The data to update.
	 *
	 * @return int|bool The number of rows updated or false on failure.
	 */
	public function update( array $data ) {
		if ( empty( $this->wheres ) ) {
			return false; // Prevent updating all rows if no where clause is set.
		}

		return $this->wpdb->update( $this->wpdb->prefix . $this->table, $data, $this->extract_where_conditions() );
	}

	/**
	 * Delete records from the table.
	 *
	 * @return int|bool The number of rows deleted or false on failure.
	 */
	public function delete() {
		if ( empty( $this->wheres ) ) {
			return false; // Prevent deleting all rows if no where clause is set.
		}

		return $this->wpdb->delete( $this->wpdb->prefix . $this->table, $this->extract_where_conditions() );
	}

	/**
	 * Extract the where conditions as an associative array.
	 *
	 * @return array The where conditions.
	 */
	protected function extract_where_conditions() {
		$conditions = array();

		foreach ( $this->wheres as $where ) {
			$conditions[ $where['column'] ] = $where['value'];
		}

		return $conditions;
	}
}
