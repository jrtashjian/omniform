<?php
/**
 * Email notifications for domain form responses.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Form;
use OmniForm\Form\Response;

/**
 * Sends plain-text notification mail for a domain Response.
 */
final class ResponseNotificationMailer {
	public function __construct(
		private readonly TextResponsePresenter $presenter = new TextResponsePresenter(),
	) {}

	/**
	 * Send notification mail when the form has recipients configured.
	 *
	 * @param Response             $response Domain response.
	 * @param Form                 $form     Domain form (with notifications when loaded).
	 * @param array<string, mixed> $context  Optional: user_ip, referer, time.
	 */
	public function send( Response $response, Form $form, array $context = array() ): void {
		$settings = $form->notifications();

		if ( null === $settings || ! $settings->has_recipients() ) {
			return;
		}

		$body = $this->presenter->present(
			$response,
			$this->footer_lines( $context )
		);

		wp_mail(
			$settings->recipients(),
			$settings->subject(),
			$body
		);
	}

	/**
	 * @param array<string, mixed> $context Mail context.
	 * @return list<string>
	 */
	private function footer_lines( array $context ): array {
		$site_url = (string) get_bloginfo( 'url' );
		$time     = isset( $context['time'] ) && is_string( $context['time'] ) && '' !== $context['time']
			? $context['time']
			: (string) current_time( 'mysql' );
		$ip       = isset( $context['user_ip'] ) ? (string) $context['user_ip'] : '';
		$referer  = isset( $context['referer'] ) ? (string) $context['referer'] : '';

		$lines = array(
			/* translators: %s: Site URL. */
			sprintf( __( 'This email was sent to notify you of a response made through the contact form on %s.', 'omniform' ), $site_url ),
			__( 'Time: ', 'omniform' ) . $time,
			__( 'IP Address: ', 'omniform' ) . $ip,
			__( 'Form URL: ', 'omniform' ) . $referer,
		);

		return $lines;
	}
}
