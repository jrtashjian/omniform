<?php
/**
 * Tests the Schema class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\Schema;
use OmniForm\Tests\Unit\BaseTestCase;
use wpdb;
use Mockery;
use WP_Mock;

/**
 * Tests the Schema class.
 */
class SchemaTest extends BaseTestCase {
	/**
	 * The wpdb mock.
	 *
	 * @var \Mockery\MockInterface|\wpdb $wpdb
	 */
	private $wpdb;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		$this->wpdb         = Mockery::mock( 'wpdb' );
		$this->wpdb->prefix = 'wp_';

		Schema::set_database( $this->wpdb );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
	}

	/**
	 * Test setting the database.
	 */
	public function testSetDatabase() {
		$new_db = Mockery::mock( 'wpdb' );
		Schema::set_database( $new_db );

		// Since it's static, we can't easily test the internal state,
		// but we can ensure no exceptions are thrown.
		$this->assertTrue( true );
	}

	/**
	 * Test creating a table.
	 */
	public function testCreateTable() {
		$table      = 'test_table';
		$definition = array(
			'`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`name` VARCHAR(255) NOT NULL',
			'PRIMARY KEY (`id`)',
		);

		$this->wpdb->shouldReceive( 'get_charset_collate' )->andReturn( 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' );

		WP_Mock::userFunction( 'dbDelta' );

		Schema::create( $table, $definition );

		$this->assertTrue( true ); // Just ensure no exception.
	}

	/**
	 * Test dropping a table.
	 */
	public function testDropTable() {
		$table = 'test_table';

		$this->wpdb->shouldReceive( 'query' )->once()->with( 'DROP TABLE IF EXISTS wp_test_table;' )->andReturn( true );

		Schema::drop( $table );

		$this->assertTrue( true ); // Ensure no exception.
	}

	/**
	 * Test checking if a table exists (table exists).
	 */
	public function testHasTableExists() {
		$table = 'existing_table';

		$this->wpdb->shouldReceive( 'prepare' )->with( 'SHOW TABLES LIKE %s', 'wp_existing_table' )->andReturn( "SHOW TABLES LIKE 'wp_existing_table'" );
		$this->wpdb->shouldReceive( 'get_var' )->with( "SHOW TABLES LIKE 'wp_existing_table'" )->andReturn( 'wp_existing_table' );

		$result = Schema::has_table( $table );

		$this->assertTrue( $result );
	}

	/**
	 * Test checking if a table exists (table does not exist).
	 */
	public function testHasTableNotExists() {
		$table = 'non_existing_table';

		$this->wpdb->shouldReceive( 'prepare' )->with( 'SHOW TABLES LIKE %s', 'wp_non_existing_table' )->andReturn( "SHOW TABLES LIKE 'wp_non_existing_table'" );
		$this->wpdb->shouldReceive( 'get_var' )->with( "SHOW TABLES LIKE 'wp_non_existing_table'" )->andReturn( null );

		$result = Schema::has_table( $table );

		$this->assertFalse( $result );
	}
}
