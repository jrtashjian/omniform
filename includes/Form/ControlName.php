<?php
/**
 * Control name composition.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Composes fieldset path + control name into a FieldPath for HTML/submission.
 */
final class ControlName {
	/**
	 * Compose a full field path from walk/render context.
	 *
	 * Choice groups (radio/checkbox) share the fieldset path and drop the
	 * individual option control name.
	 *
	 * @param FieldPath $prefix       Ancestor fieldPath segments (may be empty).
	 * @param FieldName $control      This control's name (fieldName or label fallback).
	 * @param string    $type         Control type (text, radio, checkbox, select, …).
	 * @param bool      $choice_group Whether the parent fieldset is a choice group.
	 *
	 * @throws \InvalidArgumentException If a choice group has no path prefix.
	 */
	public static function compose(
		FieldPath $prefix,
		FieldName $control,
		string $type,
		bool $choice_group = false,
	): FieldPath {
		if ( $choice_group && self::is_choice_type( $type ) ) {
			if ( $prefix->is_empty() ) {
				throw new \InvalidArgumentException( 'Choice group controls require a field path prefix.' );
			}

			return $prefix;
		}

		return $prefix->is_empty()
			? FieldPath::root( $control )
			: $prefix->append( $control );
	}

	/**
	 * Whether the HTML name should use a multi-value suffix ([]).
	 */
	public static function is_multiple(
		string $type,
		bool $choice_group = false,
		bool $multiple = false,
	): bool {
		if ( $multiple ) {
			return true;
		}

		return $choice_group && 'checkbox' === $type;
	}

	/**
	 * @param string $type Control type.
	 */
	private static function is_choice_type( string $type ): bool {
		return in_array( $type, array( 'radio', 'checkbox' ), true );
	}
}
