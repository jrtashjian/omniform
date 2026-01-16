<?php
/**
 * Tests the ParameterBag class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin\Http;

use OmniForm\Plugin\Http\ParameterBag;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ParameterBag class.
 */
class ParameterBagTest extends TestCase {
	/**
	 * Test the constructor.
	 */
	public function testConstructor() {
		$parameters = array(
			'foo' => 'bar',
			'baz' => 'qux',
		);

		$bag = new ParameterBag( $parameters );

		$this->assertEquals( $parameters, $bag->getIterator()->getArrayCopy() );
	}

	/**
	 * Test the constructor with empty array.
	 */
	public function testConstructorEmpty() {
		$bag = new ParameterBag();

		$this->assertEquals( 0, $bag->count() );
	}

	/**
	 * Test keys method.
	 */
	public function testKeys() {
		$parameters = array(
			'foo' => 'bar',
			'baz' => 'qux',
		);

		$bag = new ParameterBag( $parameters );

		$this->assertEquals( array( 'foo', 'baz' ), $bag->keys() );
	}

	/**
	 * Test get method with existing key.
	 */
	public function testGetExisting() {
		$parameters = array( 'foo' => 'bar' );

		$bag = new ParameterBag( $parameters );

		$this->assertEquals( 'bar', $bag->get( 'foo' ) );
	}

	/**
	 * Test get method with non-existing key.
	 */
	public function testGetNonExisting() {
		$bag = new ParameterBag();

		$this->assertNull( $bag->get( 'nonexistent' ) );
	}

	/**
	 * Test get method with default value.
	 */
	public function testGetWithDefault() {
		$bag = new ParameterBag();

		$this->assertEquals( 'default', $bag->get( 'nonexistent', 'default' ) );
	}

	/**
	 * Test has method with existing key.
	 */
	public function testHasExisting() {
		$parameters = array( 'foo' => 'bar' );

		$bag = new ParameterBag( $parameters );

		$this->assertTrue( $bag->has( 'foo' ) );
	}

	/**
	 * Test has method with non-existing key.
	 */
	public function testHasNonExisting() {
		$bag = new ParameterBag();

		$this->assertFalse( $bag->has( 'nonexistent' ) );
	}

	/**
	 * Test getIterator method.
	 */
	public function testGetIterator() {
		$parameters = array(
			'foo' => 'bar',
			'baz' => 'qux',
		);

		$bag = new ParameterBag( $parameters );

		$iterator = $bag->getIterator();

		$this->assertInstanceOf( \ArrayIterator::class, $iterator );
		$this->assertEquals( $parameters, $iterator->getArrayCopy() );
	}

	/**
	 * Test count method.
	 */
	public function testCount() {
		$parameters = array(
			'foo' => 'bar',
			'baz' => 'qux',
		);

		$bag = new ParameterBag( $parameters );

		$this->assertEquals( 2, $bag->count() );
	}

	/**
	 * Test count method with empty bag.
	 */
	public function testCountEmpty() {
		$bag = new ParameterBag();

		$this->assertEquals( 0, $bag->count() );
	}

	/**
	 * Test iteration.
	 */
	public function testIteration() {
		$parameters = array(
			'foo' => 'bar',
			'baz' => 'qux',
		);

		$bag = new ParameterBag( $parameters );

		$result = array();
		foreach ( $bag as $key => $value ) {
			$result[ $key ] = $value;
		}

		$this->assertEquals( $parameters, $result );
	}
}
