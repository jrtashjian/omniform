<?php
/**
 * The Schema class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * The Schema class.
 */
class Schema {
	/**
	 * Create a new table.
	 *
	 * @param string $table The table name.
	 * @param array  $definition The table definition.
	 */
	public static function create( string $table, array $definition ) {
		global $wpdb;

		$table           = $wpdb->prefix . $table;
		$definition      = implode( ', ', $definition );
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( "CREATE TABLE {$table} ({$definition}) $charset_collate;" );
	}

	/**
	 * Drop a table.
	 *
	 * @param string $table The table name.
	 */
	public static function drop( string $table ) {
		global $wpdb;

		$table = $wpdb->prefix . $table;

		$wpdb->query( "DROP TABLE IF EXISTS {$table};" ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Check if a table exists.
	 *
	 * @param string $table The table name.
	 *
	 * @return bool True if the table exists, false otherwise.
	 */
	public static function has_table( string $table ) {
		global $wpdb;

		return (bool) $wpdb->get_var(
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$wpdb->prefix . $table
			)
		);
	}
}
