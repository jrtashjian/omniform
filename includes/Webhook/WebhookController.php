<?php
/**
 * The WebhookController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Webhook;

use OmniForm\OAuth\TokenStorage;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Server;

/**
 * The WebhookController class.
 *
 * Registers the inbound webhook endpoint that the OmniForm API calls
 * to deliver async events. Requests are authenticated by an HMAC-SHA256
 * signature over the raw request body, not by WordPress nonces.
 */
class WebhookController extends WP_REST_Controller {

	/**
	 * The TokenStorage instance.
	 *
	 * @var TokenStorage
	 */
	protected $token_storage;

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'omniform/v1';

	/**
	 * The rest base.
	 *
	 * @var string
	 */
	protected $rest_base = 'webhook';

	/**
	 * Constructor.
	 *
	 * @param TokenStorage $token_storage The TokenStorage instance.
	 */
	public function __construct( TokenStorage $token_storage ) {
		$this->token_storage = $token_storage;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_webhook' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to the webhook.
	 *
	 * The endpoint is public: the OmniForm API cannot send a WordPress
	 * nonce, so authentication happens via the HMAC signature in
	 * handle_webhook() instead of a capability check here.
	 *
	 * @return true True. Signature verification gates the request.
	 */
	public function permissions_check() {
		return true;
	}

	/**
	 * Handles an inbound webhook from the OmniForm API.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|array WP_Error on failure, or the received response.
	 */
	public function handle_webhook( WP_REST_Request $request ) {
		$raw_body = $request->get_body();
		$secret   = $this->token_storage->get_webhook_secret();

		if ( ! $secret ) {
			return new WP_Error(
				'omniform_webhook_not_configured',
				__( 'Webhook secret not configured.', 'omniform' ),
				array( 'status' => 403 )
			);
		}

		$signature = $request->get_header( 'x_omniform_signature' );

		if ( ! $signature ) {
			return new WP_Error(
				'omniform_webhook_missing_signature',
				__( 'Missing webhook signature.', 'omniform' ),
				array( 'status' => 401 )
			);
		}

		$computed = hash_hmac( 'sha256', $raw_body, $secret );

		if ( ! hash_equals( $computed, $signature ) ) {
			return new WP_Error(
				'omniform_webhook_invalid_signature',
				__( 'Invalid webhook signature.', 'omniform' ),
				array( 'status' => 401 )
			);
		}

		$payload = json_decode( $raw_body, true );

		if ( ! is_array( $payload ) || empty( $payload['type'] ) || ! is_string( $payload['type'] ) ) {
			return new WP_Error(
				'omniform_webhook_invalid_payload',
				__( 'Invalid webhook payload.', 'omniform' ),
				array( 'status' => 400 )
			);
		}

		do_action( 'omniform_webhook_received', $payload );

		return array( 'received' => true );
	}
}
