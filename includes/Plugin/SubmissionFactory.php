<?php
/**
 * Submission factory from HTTP request data.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Submission;

/**
 * Builds a domain Submission from request parameters and uploaded files.
 *
 * Security boundary: strips transport noise, sanitizes text, and reduces file
 * entries to plain metadata (no tmp_name paths).
 */
class SubmissionFactory {
	/**
	 * Default request keys excluded from submissions.
	 *
	 * @var list<string>
	 */
	private const DEFAULT_EXCLUDED_KEYS = array(
		'id',
		'rest_route',
		'wp_rest',
		'_locale',
		'_wp_http_referer',
		'_wpnonce',
		'omniform_hash',
		'_omniform_user_ip',
	);

	/**
	 * Build a Submission from request params and optional files.
	 *
	 * @param array<string, mixed> $params Request parameters (e.g. POST body).
	 * @param array<string, mixed> $files  Uploaded files in $_FILES shape.
	 * @param FormSchema|null      $schema Parsed form schema for type-aware
	 *                                    sanitization. Null keeps legacy behavior.
	 */
	public function from_request( array $params, array $files = array(), ?FormSchema $schema = null ): Submission {
		$values = $this->sanitize_array( $this->filter_params( $params ), $schema, '' );
		$values = $this->merge_files( $values, $files );

		return new Submission( $values );
	}

	/**
	 * Remove transport / infrastructure keys.
	 *
	 * @param array<string, mixed> $params Raw request params.
	 * @return array<string, mixed>
	 */
	private function filter_params( array $params ): array {
		/**
		 * Keys excluded from form submissions.
		 *
		 * @param string[] $excluded_keys Excluded request keys.
		 */
		$excluded = apply_filters( 'omniform_filtered_request_params', self::DEFAULT_EXCLUDED_KEYS );

		if ( ! is_array( $excluded ) ) {
			$excluded = self::DEFAULT_EXCLUDED_KEYS;
		}

		return array_filter(
			$params,
			static fn( $key ): bool => ! in_array( $key, $excluded, true ),
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Recursively sanitize scalar values for storage/validation.
	 *
	 * @param mixed           $data   Raw data.
	 * @param FormSchema|null $schema Parsed form schema (null keeps legacy behavior).
	 * @param string          $prefix Dotted field path matching FormSchema keys.
	 * @return mixed
	 */
	private function sanitize_array( mixed $data, ?FormSchema $schema, string $prefix ): mixed {
		if ( is_array( $data ) ) {
			$clean = array();

			foreach ( $data as $key => $value ) {
				$clean_key           = is_string( $key ) ? sanitize_text_field( $key ) : $key;
				$child_prefix        = '' === $prefix ? (string) $key : $prefix . '.' . (string) $key;
				$clean[ $clean_key ] = $this->sanitize_array( $value, $schema, $child_prefix );
			}

			return $clean;
		}

		if ( is_bool( $data ) || is_int( $data ) || is_float( $data ) || null === $data ) {
			return $data;
		}

		return $this->sanitize_value( (string) $data, $schema, $prefix );
	}

	/**
	 * Sanitize a single string value by its declared field type.
	 *
	 * @param string          $value  Raw string value.
	 * @param FormSchema|null $schema Parsed form schema (null keeps legacy behavior).
	 * @param string          $prefix Dotted field path matching a FormSchema key.
	 * @return mixed
	 */
	private function sanitize_value( string $value, ?FormSchema $schema, string $prefix ): mixed {
		$field = '' !== $prefix && null !== $schema ? $schema->field( $prefix ) : null;

		if ( null === $schema ) {
			return sanitize_textarea_field( $value );
		}

		if ( null === $field ) {
			return sanitize_text_field( $value );
		}

		return $this->sanitize_field_value( $value, $field->type() );
	}

	/**
	 * Dispatch a value to the sanitizer matching its field type.
	 *
	 * @param string $value Raw string value.
	 * @param string $type  Field control type.
	 * @return mixed
	 */
	private function sanitize_field_value( string $value, string $type ): mixed {
		switch ( $type ) {
			case 'email':
				return sanitize_email( $value );

			case 'url':
				return sanitize_url( $value );

			case 'number':
			case 'range':
				if ( ! is_numeric( $value ) ) {
					// Let the validator reject non-numeric text with a useful message.
					return sanitize_text_field( $value );
				}

				return false !== strpos( $value, '.' ) || false !== stripos( $value, 'e' )
					? (float) $value
					: (int) $value;

			case 'textarea':
				return sanitize_textarea_field( $value );

			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Merge file field metadata into submission values.
	 *
	 * @param array<string, mixed> $values Sanitized params.
	 * @param array<string, mixed> $files  $_FILES-shaped data.
	 * @return array<string, mixed>
	 */
	private function merge_files( array $values, array $files ): array {
		foreach ( $files as $key => $file ) {
			if ( ! is_string( $key ) || '' === $key ) {
				continue;
			}

			$meta = $this->file_meta( $file );

			if ( null === $meta ) {
				continue;
			}

			$values[ sanitize_text_field( $key ) ] = $meta;
		}

		return $values;
	}

	/**
	 * Reduce a $_FILES entry to plain, non-sensitive metadata.
	 *
	 * @param mixed $file File entry.
	 * @return array<string, mixed>|list<array<string, mixed>>|null
	 */
	private function file_meta( mixed $file ): array|null {
		if ( ! is_array( $file ) ) {
			return null;
		}

		// Multi-file field: name is a list.
		if ( isset( $file['name'] ) && is_array( $file['name'] ) ) {
			$items = array();
			$count = count( $file['name'] );

			for ( $i = 0; $i < $count; $i++ ) {
				$items[] = $this->single_file_meta(
					array(
						'name'  => $file['name'][ $i ] ?? '',
						'type'  => $file['type'][ $i ] ?? '',
						'size'  => $file['size'][ $i ] ?? 0,
						'error' => $file['error'][ $i ] ?? UPLOAD_ERR_NO_FILE,
					)
				);
			}

			return $items;
		}

		if ( ! array_key_exists( 'name', $file ) ) {
			return null;
		}

		return $this->single_file_meta( $file );
	}

	/**
	 * @param array<string, mixed> $file Single file entry.
	 * @return array{name: string, type: string, size: int, error: int}
	 */
	private function single_file_meta( array $file ): array {
		$name          = isset( $file['name'] ) ? sanitize_file_name( (string) $file['name'] ) : '';
		$type          = isset( $file['type'] ) ? sanitize_text_field( (string) $file['type'] ) : '';
		$size          = isset( $file['size'] ) ? (int) $file['size'] : 0;
		$default_error = defined( 'UPLOAD_ERR_NO_FILE' ) ? UPLOAD_ERR_NO_FILE : 4;
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- error is an integer upload code.
		$error = isset( $file['error'] ) ? (int) $file['error'] : $default_error;

		return array(
			'name'  => $name,
			'type'  => $type,
			'size'  => max( 0, $size ),
			'error' => $error,
		);
	}
}
