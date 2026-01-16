<?php
/**
 * Tests the Request class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin\Http;

use OmniForm\Plugin\Http\Request;
use OmniForm\Plugin\Http\ParameterBag;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the Request class.
 */
class RequestTest extends BaseTestCase {
	/**
	 * Test the constructor.
	 */
	public function testConstructor() {
		$query   = array( 'foo' => 'bar' );
		$request = array( 'baz' => 'qux' );
		$files   = array( 'file' => 'upload' );
		$server  = array( 'host' => 'example.com' );

		$req = new Request( $query, $request, $files, $server );

		$this->assertInstanceOf( ParameterBag::class, $req->query );
		$this->assertInstanceOf( ParameterBag::class, $req->request );
		$this->assertInstanceOf( ParameterBag::class, $req->files );
		$this->assertInstanceOf( ParameterBag::class, $req->server );

		$this->assertEquals( 'bar', $req->query->get( 'foo' ) );
		$this->assertEquals( 'qux', $req->request->get( 'baz' ) );
		$this->assertEquals( 'upload', $req->files->get( 'file' ) );
		$this->assertEquals( 'example.com', $req->server->get( 'host' ) );
	}

	/**
	 * Test the constructor with empty arrays.
	 */
	public function testConstructorEmpty() {
		$req = new Request();

		$this->assertInstanceOf( ParameterBag::class, $req->query );
		$this->assertInstanceOf( ParameterBag::class, $req->request );
		$this->assertInstanceOf( ParameterBag::class, $req->files );
		$this->assertInstanceOf( ParameterBag::class, $req->server );
	}

	/**
	 * Test get_user_agent.
	 */
	public function testGetUserAgent() {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'args'   => array( 'Mozilla/5.0' ),
				'return' => 'Mozilla/5.0',
			)
		);

		$req = new Request( array(), array(), array(), array( 'HTTP_USER_AGENT' => 'Mozilla/5.0' ) );

		$result = $req->get_user_agent();

		$this->assertEquals( 'Mozilla/5.0', $result );
	}

	/**
	 * Test get_user_agent with no user agent.
	 */
	public function testGetUserAgentEmpty() {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'args'   => array( '' ),
				'return' => '',
			)
		);

		$req = new Request();

		$result = $req->get_user_agent();

		$this->assertEquals( '', $result );
	}

	/**
	 * Test get_ip_address.
	 */
	public function testGetIpAddress() {
		$req = new Request( array(), array(), array(), array( 'REMOTE_ADDR' => '192.168.1.1' ) );

		$result = $req->get_ip_address();

		$this->assertEquals( '192.168.1.1', $result );
	}

	/**
	 * Test get_ip_address with invalid IP.
	 */
	public function testGetIpAddressInvalid() {
		$req = new Request( array(), array(), array(), array( 'REMOTE_ADDR' => 'invalid' ) );

		$result = $req->get_ip_address();

		$this->assertEquals( '', $result );
	}

	/**
	 * Test get_ip_address with no IP.
	 */
	public function testGetIpAddressEmpty() {
		$req = new Request();

		$result = $req->get_ip_address();

		$this->assertEquals( '', $result );
	}
}
