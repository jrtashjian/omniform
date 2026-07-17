<?php
/**
 * Response repository.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Exceptions\InvalidResponseIdException;
use OmniForm\Exceptions\ResponseNotFoundException;
use OmniForm\Form\Field;
use OmniForm\Form\FieldGroup;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;

/**
 * Loads and saves domain Responses on the omniform_response post type.
 *
 * Response posts are self-contained snapshots: viewing never requires the
 * parent form to still exist.
 */
class ResponseRepository {
	/**
	 * Load a response by post ID.
	 *
	 * @param int $response_id The response post ID.
	 *
	 * @throws InvalidResponseIdException If the ID is not positive.
	 * @throws ResponseNotFoundException If missing or wrong post type.
	 */
	public function get( int $response_id ): Response {
		if ( $response_id < 1 ) {
			throw new InvalidResponseIdException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID must be an integer. &#8220;%s&#8221; is not a valid integer.', 'omniform' ), $response_id ) )
			);
		}

		$post = get_post( $response_id );

		if ( ! $post || 'omniform_response' !== $post->post_type ) {
			throw new ResponseNotFoundException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID &#8220;%d&#8221; does not exist.', 'omniform' ), $response_id ) )
			);
		}

		return $this->from_post( $post );
	}

	/**
	 * Hydrate a domain Response from an omniform_response post.
	 *
	 * Supports domain payloads (version/schema/submission) and legacy
	 * response/fields/groups storage using only data stored on the post.
	 *
	 * @param \WP_Post $post Response post.
	 *
	 * @throws \InvalidArgumentException If post content is not a valid payload.
	 */
	public function from_post( \WP_Post $post ): Response {
		$data = json_decode( $post->post_content, true );

		if ( ! is_array( $data ) ) {
			throw new \InvalidArgumentException(
				esc_html(
					sprintf( 'Response ID %d does not contain a valid JSON payload.', $post->ID )
				)
			);
		}

		return $this->from_payload( $data );
	}

	/**
	 * Persist a response under a form.
	 *
	 * @param Response             $response Domain response (schema + submission).
	 * @param int                  $form_id  Parent form post ID.
	 * @param array<string, mixed> $meta     Optional post meta (e.g. user IP, referer).
	 *
	 * @return int New response post ID.
	 *
	 * @throws \InvalidArgumentException If the form ID is invalid.
	 * @throws \RuntimeException If wp_insert_post fails.
	 */
	public function save( Response $response, int $form_id, array $meta = array() ): int {
		if ( $form_id < 1 ) {
			throw new \InvalidArgumentException( 'Form ID must be a positive integer.' );
		}

		$payload = wp_json_encode( $response->to_array(), JSON_UNESCAPED_UNICODE );

		if ( false === $payload ) {
			throw new \RuntimeException( 'Failed to encode response payload.' );
		}

		$meta_input = array(
			'_omniform_id' => $form_id,
		);

		if ( array_key_exists( 'user_ip', $meta ) ) {
			$meta_input['_omniform_user_ip'] = sanitize_text_field( (string) $meta['user_ip'] );
		}

		if ( array_key_exists( 'referer', $meta ) ) {
			$meta_input['_wp_http_referer'] = esc_url_raw( (string) $meta['referer'] );
		}

		$result = wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => $payload,
				'post_type'    => 'omniform_response',
				'post_status'  => 'omniform_unread',
				'post_parent'  => $form_id,
				'meta_input'   => $meta_input,
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			throw new \RuntimeException( esc_html( $result->get_error_message() ) );
		}

		return (int) $result;
	}

	/**
	 * Decode a response payload into a domain Response.
	 *
	 * @param array<string, mixed> $data Decoded post_content.
	 *
	 * @throws \InvalidArgumentException If the payload cannot be interpreted.
	 */
	private function from_payload( array $data ): Response {
		if ( isset( $data['schema'], $data['submission'] ) ) {
			return Response::from_array( $data );
		}

		return $this->from_legacy_payload( $data );
	}

	/**
	 * Rebuild a domain Response from legacy response/fields/groups JSON.
	 *
	 * @param array<string, mixed> $data Legacy or very-old flat payload.
	 */
	private function from_legacy_payload( array $data ): Response {
		if ( ! array_key_exists( 'response', $data ) ) {
			$values       = $data;
			$field_labels = $this->name_to_label_map( array_keys( $data ) );
			$group_labels = array();
		} else {
			$values       = is_array( $data['response'] ?? null ) ? $data['response'] : array();
			$field_labels = is_array( $data['fields'] ?? null ) ? $data['fields'] : array();
			$group_labels = is_array( $data['groups'] ?? null ) ? $data['groups'] : array();
		}

		if ( array() === $field_labels ) {
			$field_labels = $this->labels_from_values( $values );
		}

		return new Response(
			new FormSchema(
				$this->fields_from_labels( $field_labels ),
				$this->groups_from_labels( $group_labels )
			),
			new Submission( $values )
		);
	}

	/**
	 * Build Field objects from a name => label map.
	 *
	 * @param array<string|int, mixed> $labels Name => label map.
	 * @return list<Field>
	 */
	private function fields_from_labels( array $labels ): array {
		$fields = array();

		foreach ( $labels as $name => $label ) {
			$field = $this->try_field( (string) $name, (string) $label );
			if ( null !== $field ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Build FieldGroup objects from a name => label map.
	 *
	 * @param array<string|int, mixed> $labels Name => label map.
	 * @return list<FieldGroup>
	 */
	private function groups_from_labels( array $labels ): array {
		$groups = array();

		foreach ( $labels as $name => $label ) {
			$group = $this->try_group( (string) $name, (string) $label );
			if ( null !== $group ) {
				$groups[] = $group;
			}
		}

		return $groups;
	}

	/**
	 * Create a Field when the path and label are valid.
	 *
	 * @param string $name  Dot-separated field path key.
	 * @param string $label Human-readable label.
	 */
	private function try_field( string $name, string $label ): ?Field {
		if ( '' === $name ) {
			return null;
		}

		if ( '' === $label ) {
			$label = $name;
		}

		try {
			return new Field(
				FieldPath::from_segments( explode( '.', $name ) ),
				$label,
				'text'
			);
		} catch ( \InvalidArgumentException ) {
			return null;
		}
	}

	/**
	 * Create a FieldGroup when the path and label are valid.
	 *
	 * @param string $name  Dot-separated group path key.
	 * @param string $label Human-readable label.
	 */
	private function try_group( string $name, string $label ): ?FieldGroup {
		if ( '' === $name ) {
			return null;
		}

		if ( '' === $label ) {
			$label = $name;
		}

		try {
			return new FieldGroup(
				FieldPath::from_segments( explode( '.', $name ) ),
				$label
			);
		} catch ( \InvalidArgumentException ) {
			return null;
		}
	}

	/**
	 * Map names to themselves as labels.
	 *
	 * @param list<string|int> $names Field names used as their own labels.
	 * @return array<string, string>
	 */
	private function name_to_label_map( array $names ): array {
		$labels = array();

		foreach ( $names as $name ) {
			$key = (string) $name;
			if ( '' !== $key ) {
				$labels[ $key ] = $key;
			}
		}

		return $labels;
	}

	/**
	 * Derive labels from flattened submission value keys.
	 *
	 * @param array<string, mixed> $values Nested submission values.
	 * @return array<string, string>
	 */
	private function labels_from_values( array $values ): array {
		return $this->name_to_label_map( array_keys( $this->flatten( $values ) ) );
	}

	/**
	 * Flatten nested associative values to dotted keys.
	 *
	 * @param array<string, mixed> $data        Nested array.
	 * @param string               $path_prefix Current path prefix.
	 * @return array<string, mixed>
	 */
	private function flatten( array $data, string $path_prefix = '' ): array {
		$flat = array();

		foreach ( $data as $key => $value ) {
			$full_key = '' === $path_prefix ? (string) $key : $path_prefix . '.' . $key;

			if ( is_array( $value ) && $this->is_assoc( $value ) ) {
				$flat += $this->flatten( $value, $full_key );
			} else {
				$flat[ $full_key ] = $value;
			}
		}

		return $flat;
	}

	/**
	 * Whether an array is associative (not a list).
	 *
	 * @param array<mixed> $value Candidate array.
	 */
	private function is_assoc( array $value ): bool {
		if ( array() === $value ) {
			return false;
		}

		return array_keys( $value ) !== range( 0, count( $value ) - 1 );
	}
}
