<?php
/**
 * The Request class.
 *
 * This class was based on the Symfony HttpFoundation component (MIT License).
 * See: https://symfony.com/doc/current/components/http_foundation.html
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Http;

/**
 * The Request class.
 */
class Request {
	/**
	 * The $_GET parameters.
	 *
	 * @var ParameterBag
	 */
	public ParameterBag $query;

	/**
	 * The $_POST parameters.
	 *
	 * @var ParameterBag
	 */
	public ParameterBag $request;

	/**
	 * The $_FILES parameters.
	 *
	 * @var ParameterBag
	 */
	public ParameterBag $files;

	/**
	 * The $_SERVER parameters.
	 *
	 * @var ParameterBag
	 */
	public ParameterBag $server;

	/**
	 * The Request constructor.
	 *
	 * @param array $query   GET parameters.
	 * @param array $request POST parameters.
	 * @param array $files   FILES parameters.
	 * @param array $server  SERVER parameters.
	 */
	public function __construct( array $query = array(), array $request = array(), array $files = array(), array $server = array() ) {
		$this->query   = new ParameterBag( $query );
		$this->request = new ParameterBag( $request );
		$this->files   = new ParameterBag( $files );
		$this->server  = new ParameterBag( $server );
	}

	/**
	 * Get the User-Agent string.
	 *
	 * @return string The User-Agent string.
	 */
	public function get_user_agent() {
		return sanitize_text_field( $this->server->get( 'HTTP_USER_AGENT', '' ) );
	}

	/**
	 * Get the IP address.
	 *
	 * @return string The IP address.
	 */
	public function get_ip_address() {
		$ip = filter_var( $this->server->get( 'REMOTE_ADDR', '' ), FILTER_VALIDATE_IP );
		return $ip ? $ip : '';
	}
}
