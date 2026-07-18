<?php
/**
 * Form submission values.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable bag of submitted field values.
 *
 * Security: sanitization, allow-listing of keys, and $_FILES shaping belong in
 * the request adapter. This object only accepts plain data (scalars, null, and
 * nested arrays of the same) so the domain never holds untrusted objects.
 */
final class Submission {
	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $values Nested values keyed like request data.
	 *
	 * @throws \InvalidArgumentException If values contain non-plain data.
	 */
	public function __construct(
		private readonly array $values = array()
	) {
		$this->assert_plain( $this->values );
	}

	/**
	 * All submitted values.
	 *
	 * @return array<string, mixed>
	 */
	public function values(): array {
		return $this->values;
	}

	/**
	 * Value at a composed field path, or null when missing.
	 *
	 * @param FieldPath $path Composed field path.
	 * @return mixed|null
	 */
	public function value( FieldPath $path ) {
		$resolved = $this->resolve( $path );

		return null === $resolved ? null : $resolved[0];
	}

	/**
	 * Whether a path has a submitted value (including empty string / empty array).
	 *
	 * @param FieldPath $path Composed field path.
	 */
	public function has( FieldPath $path ): bool {
		return null !== $this->resolve( $path );
	}

	/**
	 * Serialize for persistence.
	 *
	 * @return array{values: array<string, mixed>}
	 */
	public function to_array(): array {
		return array(
			'values' => $this->values,
		);
	}

	/**
	 * Restore from serialized array.
	 *
	 * @param array<string, mixed> $data Serialized submission.
	 *
	 * @throws \InvalidArgumentException If values are not an array or are non-plain.
	 */
	public static function from_array( array $data ): self {
		$values = $data['values'] ?? $data;

		if ( ! is_array( $values ) ) {
			throw new \InvalidArgumentException( 'Submission values must be an array.' );
		}

		return new self( $values );
	}

	/**
	 * Walk a path when every segment exists.
	 *
	 * Distinguishes a present null from a missing path: present values are
	 * returned as a one-element list; missing paths yield null.
	 *
	 * @param FieldPath $path Composed field path.
	 * @return array{0: mixed}|null
	 */
	private function resolve( FieldPath $path ): ?array {
		$cursor = $this->values;

		foreach ( $path->segments() as $segment ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $segment, $cursor ) ) {
				return null;
			}

			$cursor = $cursor[ $segment ];
		}

		return array( $cursor );
	}

	/**
	 * Reject non-plain values (objects, resources, callables).
	 *
	 * @param mixed $data Data to validate.
	 *
	 * @throws \InvalidArgumentException If data is not plain.
	 */
	private function assert_plain( mixed $data ): void {
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ! is_string( $key ) && ! is_int( $key ) ) {
					throw new \InvalidArgumentException( 'Submission array keys must be strings or integers.' );
				}

				$this->assert_plain( $value );
			}

			return;
		}

		if ( null === $data || is_scalar( $data ) ) {
			return;
		}

		throw new \InvalidArgumentException( 'Submission values must be plain data; complex types are not allowed.' );
	}
}
