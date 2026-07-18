<?php
/**
 * WordPress-bound form render settings.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * Resolves password protection, submit attributes, and required-label text for form rendering.
 *
 * Domain Form holds content/title/status/id only; these WP details stay in the plugin layer.
 */
class FormRenderSettings {
	/**
	 * Whether the form post is password protected for the current request.
	 *
	 * @param int $form_id Form post ID.
	 */
	public function is_password_protected( int $form_id ): bool {
		return post_password_required( $form_id );
	}

	/**
	 * HTTP method for the form element.
	 *
	 * Standard forms always use POST. Custom types use post meta when set.
	 *
	 * @param int $form_id Form post ID.
	 */
	public function submit_method( int $form_id ): string {
		$submit_method = get_post_meta( $form_id, 'submit_method', true );

		return empty( $submit_method ) || 'standard' === $this->form_type( $form_id )
			? 'POST'
			: (string) $submit_method;
	}

	/**
	 * Form action URL.
	 *
	 * Standard forms post to the REST responses endpoint. Custom types use post meta when set.
	 *
	 * @param int $form_id Form post ID.
	 */
	public function submit_action( int $form_id ): string {
		$submit_action = get_post_meta( $form_id, 'submit_action', true );

		return empty( $submit_action ) || 'standard' === $this->form_type( $form_id )
			? rest_url( 'omniform/v1/forms/' . $form_id . '/responses' )
			: (string) $submit_action;
	}

	/**
	 * Required-field indicator text (default *).
	 *
	 * @param int|null    $form_id  Form post ID for meta lookup, or null for content-only forms.
	 * @param string|null $override Explicit override (e.g. standalone block attribute).
	 */
	public function required_label( ?int $form_id = null, ?string $override = null ): string {
		if ( null !== $override && '' !== $override ) {
			return $override;
		}

		if ( null !== $form_id && $form_id > 0 ) {
			$from_meta = get_post_meta( $form_id, 'required_label', true );
			if ( is_string( $from_meta ) && '' !== $from_meta ) {
				return $from_meta;
			}
		}

		return '*';
	}

	/**
	 * Form type taxonomy slug, or standard when unset.
	 *
	 * @param int $form_id Form post ID.
	 */
	private function form_type( int $form_id ): string {
		$type_terms = get_the_terms( $form_id, 'omniform_type' );

		return ! is_wp_error( $type_terms ) && false !== $type_terms
			? $type_terms[0]->slug
			: 'standard';
	}
}
