<?php
/**
 * The OAuthConnectionUI class.
 *
 * @package OmniForm
 */

namespace OmniForm\OAuth;

use OmniForm\Dependencies\League\Container\Container;

/**
 * The OAuthConnectionUI class.
 */
class OAuthConnectionUI {
	/**
	 * Container.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container The container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Check if the API is connected.
	 *
	 * @return bool True if connected, false otherwise.
	 */
	public function is_connected(): bool {
		$token_storage = $this->container->get( TokenStorage::class );
		return ! empty( $token_storage->get_access_token() );
	}

	/**
	 * Check if the API is disconnected.
	 *
	 * @return bool True if disconnected, false otherwise.
	 */
	public function is_disconnected(): bool {
		return ! $this->is_connected();
	}

	/**
	 * Render the OAuth settings page.
	 *
	 * @return void
	 */
	public function render(): void {
		$api_client    = $this->container->get( ApiClient::class );
		$token_storage = $this->container->get( TokenStorage::class );

		$data = array();

		$response = $api_client->get( '/user' );

		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ) );
		}

		$debug_data = array(
			'client_id'     => $token_storage->get_client_id(),
			'access_token'  => $token_storage->get_access_token() ? 'set' : 'not set',
			'refresh_token' => $token_storage->get_refresh_token() ? 'set' : 'not set',
			'token_expires' => get_option( 'omniform_token_expires' ),
			'code_verifier' => get_transient( 'omniform_oauth_code_verifier' ) ? 'set' : 'not set',
		);
		?>
		<div id="omniform">
			<?php $this->render_buttons(); ?>

			<h3>API Health Check</h3>
			<?php
			$healthcheck = $this->get_health_status();

			$status_message = $healthcheck['healthy']
				/* translators: %s is the latency time */
				? sprintf( __( 'The API is available (%s)', 'omniform' ), $healthcheck['latency'] )
				: sprintf( __( 'The API is unavailable', 'omniform' ) );
			?>
			<p><?php echo esc_html( $status_message ); ?></p>

			<h3>API Response</h3>
			<pre>
			<?php
			if ( ! is_wp_error( $response ) ) {
				echo esc_html( 'Response Code: ' . wp_remote_retrieve_response_code( $response ) . "\n" );
				echo esc_html( 'Response Body: ' . print_r( json_decode( wp_remote_retrieve_body( $response ), true ), true ) );
			} else {
				echo esc_html( 'Error: ' . $response->get_error_message() );
			}
			?>
			</pre>

			<h3>OAuth Debug</h3>
			<pre>
			<?php
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			print_r( $debug_data );
			?>
			</pre>
		</div>
		<?php
	}

	/**
	 * Render the connect and disconnect buttons.
	 *
	 * @return void
	 */
	private function render_buttons(): void {
		if ( $this->is_connected() ) :
			?>
			<form method="post">
				<?php wp_nonce_field( 'disconnect_api' ); ?>
				<button type="submit" name="disconnect_api" class="button">Disconnect from API</button>
			</form>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'connect_api' ); ?>
				<button type="submit" name="connect_api" class="button button-primary">Connect to API</button>
			</form>
			<?php
		endif;
	}

	/**
	 * Get the API health status.
	 *
	 * @return array Health status data.
	 */
	private function get_health_status(): array {
		$api_client = $this->container->get( ApiClient::class );

		$start    = microtime( true );
		$response = $api_client->healthcheck();
		$latency  = microtime( true ) - $start;

		return array(
			'healthy' => ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200,
			'latency' => round( $latency * 1000 ) . 'ms',
		);
	}
}
