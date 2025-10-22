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
		$this->render_buttons();
		?>
		<h3>API Health Check</h3>
		<?php
		$healthcheck = $this->get_health_status();

		$status_message = $healthcheck['healthy']
			/* translators: %s is the latency time */
			? sprintf( __( 'The API is available (%s)', 'omniform' ), $healthcheck['latency'] )
			: __( 'The API is unavailable', 'omniform' );
		?>
		<p><?php echo esc_html( $status_message ); ?></p>
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
				<button type="submit" name="disconnect_api" class="button"><?php esc_html_e( 'Disconnect from API', 'omniform' ); ?></button>
			</form>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'connect_api' ); ?>
				<button type="submit" name="connect_api" class="button button-primary"><?php esc_html_e( 'Connect to API', 'omniform' ); ?></button>
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
