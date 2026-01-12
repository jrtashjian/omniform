<?php
/**
 * The Schema class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use wpdb;

/**
 * The Schema class.
 */
class Schema {
	/**
	 * The WordPress database class.
	 *
	 * @var wpdb
	 */
	protected static $database;

	/**
	 * Create a new Schema instance.
	 *
	 * @param wpdb $wpdb The WordPress database class.
	 */
	public static function set_database( wpdb $wpdb ) {
		self::$database = $wpdb;
	}

	/**
	 * Create a new table.
	 *
	 * @param string $table The table name.
	 * @param array  $definition The table definition.
	 */
	public static function create( string $table, array $definition ) {
		$table           = self::$database->prefix . $table;
		$definition      = implode( ', ', $definition );
		$charset_collate = self::$database->get_charset_collate();

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		dbDelta( "CREATE TABLE {$table} ({$definition}) $charset_collate;" );
	}

	/**
	 * Drop a table.
	 *
	 * @param string $table The table name.
	 */
	public static function drop( string $table ) {
		$table = self::$database->prefix . $table;

		self::$database->query( "DROP TABLE IF EXISTS {$table};" ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Check if a table exists.
	 *
	 * @param string $table The table name.
	 *
	 * @return bool True if the table exists, false otherwise.
	 */
	public static function has_table( string $table ) {
		return (bool) self::$database->get_var(
			self::$database->prepare(
				'SHOW TABLES LIKE %s',
				self::$database->prefix . $table
			)
		);
	}
}
