<?php
/**
 * Field value formatting for presentation.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\Field;

/**
 * Turns a submitted field value into a human-readable string.
 */
final class FieldValueFormatter {
	/**
	 * Format a value for display next to its field label.
	 *
	 * @param Field $field Field definition (type/label for special cases).
	 * @param mixed $value Submitted value, or null when missing.
	 */
	public function format( Field $field, mixed $value ): string {
		if ( null === $value ) {
			return '';
		}

		if ( is_array( $value ) ) {
			return $this->format_array( $value );
		}

		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$string = (string) $value;

		if ( $this->is_checked_lone_checkbox( $field, $string ) ) {
			return __( 'Checked', 'omniform' );
		}

		return $string;
	}

	/**
	 * @param array<mixed> $value Nested or list value.
	 */
	private function format_array( array $value ): string {
		if ( $this->is_file_meta( $value ) ) {
			return (string) ( $value['name'] ?? '' );
		}

		$parts = array();

		foreach ( $value as $item ) {
			if ( is_array( $item ) && $this->is_file_meta( $item ) ) {
				$parts[] = (string) ( $item['name'] ?? '' );
				continue;
			}

			if ( is_scalar( $item ) && '' !== (string) $item ) {
				$parts[] = (string) $item;
			}
		}

		return implode( ', ', $parts );
	}

	/**
	 * Lone checkbox blocks submit the label as the value when checked.
	 */
	private function is_checked_lone_checkbox( Field $field, string $value ): bool {
		return 'checkbox' === $field->type()
			&& ! $field->name()->is_empty()
			&& $field->label() === $value;
	}

	/**
	 * @param array<mixed> $value Candidate file meta bag.
	 */
	private function is_file_meta( array $value ): bool {
		return array_key_exists( 'name', $value )
			&& array_key_exists( 'size', $value )
			&& ! array_is_list( $value );
	}
}
