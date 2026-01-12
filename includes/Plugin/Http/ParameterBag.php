<?php
/**
 * The ParameterBag class.
 *
 * This class was based on the Symfony HttpFoundation component (MIT License).
 * See: https://symfony.com/doc/current/components/http_foundation.html
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Http;

/**
 * The ParameterBag class.
 */
class ParameterBag implements \IteratorAggregate, \Countable {
	/**
	 * The parameters.
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * The ParameterBag constructor.
	 *
	 * @param array $parameters The parameters.
	 */
	public function __construct( array $parameters = array() ) {
		$this->parameters = $parameters;
	}

	/**
	 * Returns all the parameters.
	 *
	 * @return array The parameters.
	 */
	public function keys(): array {
		return array_keys( $this->parameters );
	}

	/**
	 * Returns a parameter by name.
	 *
	 * @param string $key     The parameter name.
	 * @param mixed  $default The default value if the parameter does not exist.
	 *
	 * @return mixed The parameter value.
	 */
	public function get( string $key, $default = null ) {
		return \array_key_exists( $key, $this->parameters ) ? $this->parameters[ $key ] : $default;
	}

	/**
	 * Checks if a parameter exists.
	 *
	 * @param string $key The parameter name.
	 *
	 * @return bool True if the parameter exists, false otherwise.
	 */
	public function has( string $key ): bool {
		return \array_key_exists( $key, $this->parameters );
	}

	/**
	 * Returns an iterator for parameters.
	 *
	 * @return \ArrayIterator An iterator for the parameters.
	 */
	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator( $this->parameters );
	}

	/**
	 * Returns the number of parameters.
	 *
	 * @return int The number of parameters.
	 */
	public function count(): int {
		return \count( $this->parameters );
	}
}
