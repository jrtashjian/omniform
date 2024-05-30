<?php
/**
 * The DB facade.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Facades;

use OmniForm\Plugin\QueryBuilder;

/**
 * The DB facade.
 */
class DB {
	/**
	 * The container instance.
	 *
	 * @var mixed
	 */
	protected static $container;

	/**
	 * Set the container instance.
	 *
	 * @param mixed $container The container instance.
	 */
	public static function set_container( $container ) {
		self::$container = $container;
	}

	/**
	 * Get the accessor.
	 *
	 * @return mixed The container instance.
	 */
	protected static function get_facade_accessor() {
		return QueryBuilder::class;
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param string $method The method name.
	 * @param array  $args The method arguments.
	 *
	 * @return mixed The method result.
	 */
	public static function __callStatic( $method, $args ) {
		$instance = self::$container->get( self::get_facade_accessor() );
		return $instance->$method( ...$args );
	}
}
