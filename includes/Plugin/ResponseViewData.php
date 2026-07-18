<?php
/**
 * Dashboard / REST view data for a domain Response.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Response;

/**
 * Builds list and detail payloads from a dual-read domain Response.
 *
 * Works for domain (version/schema/submission) and legacy snapshots once
 * hydrated by ResponseRepository.
 */
final class ResponseViewData {
	public function __construct(
		private readonly FieldValueFormatter $formatter = new FieldValueFormatter(),
	) {}

	/**
	 * Ordered field rows for the response detail panel.
	 *
	 * @return list<array{name: string, label: string, type: string, value: string}>
	 */
	public function fields( Response $response ): array {
		$rows = array();

		foreach ( $response->schema()->fields() as $field ) {
			$rows[] = array(
				'name'  => $field->name()->key(),
				'label' => $field->label(),
				'type'  => $field->type(),
				'value' => $this->formatter->format(
					$field,
					$response->submission()->value( $field->name() )
				),
			);
		}

		return $rows;
	}

	/**
	 * First plausible sender email from the submission.
	 *
	 * Prefers email-type schema fields, then any email-shaped value.
	 */
	public function sender_email( Response $response ): ?string {
		$fallback = null;

		foreach ( $response->schema()->fields() as $field ) {
			$email = $this->first_email_in(
				$response->submission()->value( $field->name() )
			);

			if ( null === $email ) {
				continue;
			}

			if ( in_array( $field->type(), array( 'email', 'username-email' ), true ) ) {
				return $email;
			}

			if ( null === $fallback ) {
				$fallback = $email;
			}
		}

		if ( null !== $fallback ) {
			return $fallback;
		}

		return $this->first_email_in( $response->submission()->values() );
	}

	/**
	 * Recursively find the first valid email string.
	 *
	 * @param mixed $value Submitted value tree.
	 */
	private function first_email_in( mixed $value ): ?string {
		if ( is_string( $value ) && is_email( $value ) ) {
			return $value;
		}

		if ( ! is_array( $value ) ) {
			return null;
		}

		foreach ( $value as $item ) {
			$found = $this->first_email_in( $item );
			if ( null !== $found ) {
				return $found;
			}
		}

		return null;
	}
}
