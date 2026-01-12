<?php
/**
 * Tests the QueryBuilder class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\QueryBuilder;
use PHPUnit\Framework\TestCase;
use wpdb;
use Mockery;
use OmniForm\Exceptions\QueryBuilderException;

/**
 * Tests the QueryBuilder class.
 */
class QueryBuilderTest extends TestCase {
	/**
	 * The wpdb mock.
	 *
	 * @var \Mockery\MockInterface|\wpdb $wpdb
	 */
	private $wpdb;

	/**
	 * The QueryBuilder instance.
	 *
	 * @var QueryBuilder
	 */
	private $query_builder;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {

		$this->wpdb          = Mockery::mock( 'wpdb' );
		$this->wpdb->prefix  = 'wp_';
		$this->query_builder = new QueryBuilder( $this->wpdb );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
	}

	/**
	 * Test basic select query generation.
	 */
	public function testBasicSelectQuery() {
		$this->query_builder->select( array( 'id', 'name' ) )->table( 'users' );

		$this->wpdb->shouldReceive( 'get_results' )->once()->with( 'SELECT id, name FROM `wp_users`' )->andReturn( array() );

		$result = $this->query_builder->get();

		$this->assertEquals( array(), $result );
	}

	/**
	 * Test select query with where clause.
	 */
	public function testSelectWithWhere() {
		$this->query_builder->select( array( 'id', 'name' ) )->table( 'users' )->where( 'status', '=', 'active' );

		$this->wpdb->shouldReceive( 'prepare' )->once()->with( '`status` = %s', array( 'active' ) )->andReturn( "`status` = 'active'" );
		$this->wpdb->shouldReceive( 'get_results' )->once()->with( "SELECT id, name FROM `wp_users` WHERE `status` = 'active'" )->andReturn(
			array(
				(object) array(
					'id'   => 1,
					'name' => 'John',
				),
			)
		);

		$result = $this->query_builder->get();

		$this->assertEquals(
			array(
				(object) array(
					'id'   => 1,
					'name' => 'John',
				),
			),
			$result
		);
	}

	/**
	 * Test select query with order by.
	 */
	public function testSelectWithOrderBy() {
		$this->query_builder->select( array( 'id', 'name' ) )->table( 'users' )->order_by( 'name' );

		$this->wpdb->shouldReceive( 'get_results' )->once()->with( 'SELECT id, name FROM `wp_users` ORDER BY `name` ASC' )->andReturn( array() );

		$result = $this->query_builder->get();

		$this->assertEquals( array(), $result );
	}

	/**
	 * Test select query with group by.
	 */
	public function testSelectWithGroupBy() {
		$this->query_builder->select( array( 'category', 'COUNT(*)' ) )->table( 'posts' )->group_by( 'category' );

		$this->wpdb->shouldReceive( 'get_results' )->once()->with( 'SELECT category, COUNT(*) FROM `wp_posts` GROUP BY category' )->andReturn( array() );

		$result = $this->query_builder->get();

		$this->assertEquals( array(), $result );
	}

	/**
	 * Test select query with limit.
	 */
	public function testSelectWithLimit() {
		$this->query_builder->select( array( 'id', 'name' ) )->table( 'users' )->limit( 10 );

		$this->wpdb->shouldReceive( 'get_results' )->once()->with( 'SELECT id, name FROM `wp_users` LIMIT 10' )->andReturn( array() );

		$result = $this->query_builder->get();

		$this->assertEquals( array(), $result );
	}

	/**
	 * Test count query.
	 */
	public function testCountQuery() {
		$this->query_builder->table( 'users' );

		$this->wpdb->shouldReceive( 'get_var' )->once()->with( 'SELECT COUNT(*) FROM `wp_users`' )->andReturn( 5 );

		$result = $this->query_builder->count();

		$this->assertEquals( 5, $result );
	}

	/**
	 * Test count query with where.
	 */
	public function testCountWithWhere() {
		$this->query_builder->table( 'users' )->where( 'status', '=', 'active' );

		$this->wpdb->shouldReceive( 'prepare' )->once()->with( '`status` = %s', array( 'active' ) )->andReturn( "`status` = 'active'" );
		$this->wpdb->shouldReceive( 'get_var' )->once()->with( "SELECT COUNT(*) FROM `wp_users` WHERE `status` = 'active'" )->andReturn( 3 );

		$result = $this->query_builder->count();

		$this->assertEquals( 3, $result );
	}

	/**
	 * Test the insert method.
	 */
	public function testInsert() {
		$this->query_builder->table( 'users' );

		$this->wpdb->shouldReceive( 'insert' )->once()->with(
			'wp_users',
			array(
				'name'  => 'John',
				'email' => 'john@example.com',
			)
		)->andReturn( 1 );

		$result = $this->query_builder->insert(
			array(
				'name'  => 'John',
				'email' => 'john@example.com',
			)
		);

		$this->assertEquals( 1, $result );
	}

	/**
	 * Test the get_last_insert_id method.
	 */
	public function testGetLastInsertId() {
		$this->wpdb->insert_id = 123;

		$result = $this->query_builder->get_last_insert_id();

		$this->assertEquals( 123, $result );
	}

	/**
	 * Test the update method.
	 */
	public function testUpdate() {
		$this->query_builder->table( 'users' )->where( 'id', '=', 1 );

		$this->wpdb->shouldReceive( 'update' )->once()->with( 'wp_users', array( 'name' => 'Jane' ), array( 'id' => 1 ) )->andReturn( 1 );

		$result = $this->query_builder->update( array( 'name' => 'Jane' ) );

		$this->assertEquals( 1, $result );
	}

	/**
	 * Test the update method without where clause.
	 */
	public function testUpdateWithoutWhere() {
		$this->query_builder->table( 'users' );

		$this->expectException( QueryBuilderException::class );

		$this->query_builder->update( array( 'name' => 'Jane' ) );
	}

	/**
	 * Test the delete method.
	 */
	public function testDelete() {
		$this->query_builder->table( 'users' )->where( 'id', '=', 1 );

		$this->wpdb->shouldReceive( 'delete' )->once()->with( 'wp_users', array( 'id' => 1 ) )->andReturn( 1 );

		$result = $this->query_builder->delete();

		$this->assertEquals( 1, $result );
	}

	/**
	 * Test the delete method without where clause.
	 */
	public function testDeleteWithoutWhere() {
		$this->query_builder->table( 'users' );

		$this->expectException( QueryBuilderException::class );

		$this->query_builder->delete();
	}
}
