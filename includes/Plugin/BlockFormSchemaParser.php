<?php
/**
 * Block form schema parser.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Form\ControlName;
use OmniForm\Form\Field;
use OmniForm\Form\FieldGroup;
use OmniForm\Form\FieldName;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;

/**
 * Builds a FormSchema from serialized block markup via parse_blocks().
 */
class BlockFormSchemaParser {
	/**
	 * Parse form block content into a FormSchema.
	 *
	 * @param string $content Serialized block markup.
	 */
	public function parse( string $content ): FormSchema {
		$fields = array();
		$groups = array();

		$this->walk(
			parse_blocks( $content ),
			FieldPath::empty(),
			array(),
			$fields,
			$groups
		);

		return new FormSchema( $fields, $groups );
	}

	/**
	 * Recursively walk blocks to extract fields and groups.
	 *
	 * @param list<array<string, mixed>> $blocks        Parsed blocks.
	 * @param FieldPath                  $path_prefix   Ancestor fieldset path.
	 * @param list<string>               $parent_rules  Rules inherited from required fieldsets.
	 * @param list<Field>                $fields        Accumulated fields.
	 * @param list<FieldGroup>           $groups        Accumulated groups.
	 */
	private function walk(
		array $blocks,
		FieldPath $path_prefix,
		array $parent_rules,
		array &$fields,
		array &$groups
	): void {
		foreach ( $blocks as $block ) {
			$name = $block['blockName'] ?? null;

			if ( null === $name || '' === $name ) {
				continue;
			}

			if ( 'omniform/fieldset' === $name ) {
				$this->walk_fieldset( $block, $path_prefix, $fields, $groups );
				continue;
			}

			if ( 'omniform/field' === $name ) {
				$field = $this->field_from_field_block( $block, $path_prefix, $parent_rules );

				if ( null !== $field ) {
					$fields[] = $field;
				}

				continue;
			}

			if ( 'omniform/hidden' === $name ) {
				$field = $this->field_from_hidden( $block, $path_prefix );

				if ( null !== $field ) {
					$fields[] = $field;
				}

				continue;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->walk( $block['innerBlocks'], $path_prefix, $parent_rules, $fields, $groups );
			}
		}
	}

	/**
	 * Recursively walk a fieldset block to extract fields and groups.
	 *
	 * @param array<string, mixed> $block       Fieldset block.
	 * @param FieldPath            $path_prefix Ancestor path.
	 * @param list<Field>          $fields      Accumulated fields.
	 * @param list<FieldGroup>     $groups      Accumulated groups.
	 */
	private function walk_fieldset(
		array $block,
		FieldPath $path_prefix,
		array &$fields,
		array &$groups
	): void {
		$attrs = $block['attrs'] ?? array();
		$label = (string) ( $attrs['fieldLabel'] ?? '' );
		$inner = $block['innerBlocks'] ?? array();

		if ( '' === $label ) {
			$this->walk( $inner, $path_prefix, array(), $fields, $groups );
			return;
		}

		$group_name = $this->field_name_from_attrs( $attrs, $label );

		if ( null === $group_name ) {
			$this->walk( $inner, $path_prefix, array(), $fields, $groups );
			return;
		}

		$group_path  = $this->append_path( $path_prefix, $group_name );
		$rules       = ! empty( $attrs['isRequired'] ) ? array( 'required' ) : array();
		$choice_type = $this->choice_group_type( $inner );

		$groups[] = new FieldGroup(
			name: $group_path,
			label: $label,
			rules: $rules,
			choice_group: null !== $choice_type,
		);

		if ( null !== $choice_type ) {
			$fields[] = new Field(
				name: $group_path,
				label: $label,
				type: $choice_type,
				rules: $rules,
			);

			return;
		}

		$this->walk( $inner, $group_path, $rules, $fields, $groups );
	}

	/**
	 * Build a Field from a field block.
	 *
	 * @param array<string, mixed> $block        Field block.
	 * @param FieldPath            $path_prefix  Ancestor path.
	 * @param list<string>         $parent_rules Rules from required parent fieldset.
	 *
	 * @return Field|null  Field or null if the block is invalid.
	 */
	private function field_from_field_block(
		array $block,
		FieldPath $path_prefix,
		array $parent_rules
	): ?Field {
		$attrs = $block['attrs'] ?? array();
		$label = (string) ( $attrs['fieldLabel'] ?? '' );

		if ( '' === $label ) {
			return null;
		}

		$control = $this->find_control( $block['innerBlocks'] ?? array() );

		if ( null === $control ) {
			return null;
		}

		$control_name = $this->field_name_from_attrs( $attrs, $label );

		if ( null === $control_name ) {
			return null;
		}

		$type = $this->control_type( $control );
		$path = ControlName::compose( $path_prefix, $control_name, $type, false );

		return new Field(
			name: $path,
			label: $label,
			type: $type,
			rules: $this->merge_required( $parent_rules, ! empty( $attrs['isRequired'] ) ),
		);
	}

	/**
	 * Build a Field from a hidden control block.
	 *
	 * @param array<string, mixed> $block       Hidden block.
	 * @param FieldPath            $path_prefix Ancestor path.
	 */
	private function field_from_hidden( array $block, FieldPath $path_prefix ): ?Field {
		$attrs = $block['attrs'] ?? array();
		$raw   = (string) ( $attrs['fieldName'] ?? '' );

		if ( '' === $raw ) {
			return null;
		}

		try {
			$control_name = FieldName::of( $raw );
		} catch ( \InvalidArgumentException $_exception ) {
			return null;
		}

		$path = ControlName::compose( $path_prefix, $control_name, 'hidden', false );

		return new Field(
			name: $path,
			label: $control_name->value(),
			type: 'hidden',
		);
	}

	/**
	 * Prefer fieldName, else label; null when sanitation fails.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @param string               $label Fallback label for name inference.
	 */
	private function field_name_from_attrs( array $attrs, string $label ): ?FieldName {
		try {
			return FieldName::from_name_or_label(
				isset( $attrs['fieldName'] ) ? (string) $attrs['fieldName'] : null,
				$label
			);
		} catch ( \InvalidArgumentException $_exception ) {
			return null;
		}
	}

	/**
	 * Append required to rules when marked and not already present.
	 *
	 * @param list<string> $rules       Existing rules.
	 * @param bool         $is_required Whether the block is required.
	 * @return list<string>
	 */
	private function merge_required( array $rules, bool $is_required ): array {
		if ( $is_required && ! in_array( 'required', $rules, true ) ) {
			$rules[] = 'required';
		}

		return $rules;
	}

	/**
	 * Append a name segment to a path prefix.
	 *
	 * @param FieldPath $prefix Ancestor path.
	 * @param FieldName $name   Segment to append.
	 */
	private function append_path( FieldPath $prefix, FieldName $name ): FieldPath {
		return $prefix->is_empty()
			? FieldPath::root( $name )
			: $prefix->append( $name );
	}

	/**
	 * Find the first input, textarea, or select control under a field.
	 *
	 * @param list<array<string, mixed>> $blocks Inner blocks of a field.
	 * @return array<string, mixed>|null
	 */
	private function find_control( array $blocks ): ?array {
		foreach ( $blocks as $block ) {
			$name = $block['blockName'] ?? '';

			if ( in_array( $name, array( 'omniform/input', 'omniform/textarea', 'omniform/select' ), true ) ) {
				return $block;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$found = $this->find_control( $block['innerBlocks'] );

				if ( null !== $found ) {
					return $found;
				}
			}
		}

		return null;
	}

	/**
	 * Map a control block to its form field type string.
	 *
	 * @param array<string, mixed> $control Control block.
	 */
	private function control_type( array $control ): string {
		return match ( $control['blockName'] ?? '' ) {
			'omniform/textarea' => 'textarea',
			'omniform/select' => 'select',
			'omniform/hidden' => 'hidden',
			default => (string) ( $control['attrs']['fieldType'] ?? 'text' ),
		};
	}

	/**
	 * First control type when every field under the tree is radio/checkbox; else null.
	 *
	 * @param list<array<string, mixed>> $blocks Fieldset inner blocks.
	 */
	private function choice_group_type( array $blocks ): ?string {
		$types = $this->field_control_types( $blocks );

		if ( array() === $types ) {
			return null;
		}

		foreach ( $types as $type ) {
			if ( ! in_array( $type, array( 'radio', 'checkbox' ), true ) ) {
				return null;
			}
		}

		return $types[0];
	}

	/**
	 * Depth-first control types of every omniform/field under the tree.
	 * Fields without a control contribute an empty string (not a choice).
	 *
	 * @param list<array<string, mixed>> $blocks Parsed blocks.
	 * @return list<string>
	 */
	private function field_control_types( array $blocks ): array {
		$types = array();

		foreach ( $blocks as $block ) {
			if ( 'omniform/field' === ( $block['blockName'] ?? '' ) ) {
				$control = $this->find_control( $block['innerBlocks'] ?? array() );
				$types[] = null === $control ? '' : $this->control_type( $control );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$types = array_merge( $types, $this->field_control_types( $block['innerBlocks'] ) );
			}
		}

		return $types;
	}
}
