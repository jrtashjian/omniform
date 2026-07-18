<?php
/**
 * Form schema.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Ordered catalog of fields and groups that describe a form's data shape.
 *
 * Used for validation and frozen into response snapshots. Built by adapters
 * (e.g. block parsers), never by inspecting WordPress types here.
 */
final class FormSchema {
	/**
	 * Constructor.
	 *
	 * @param array $fields Form fields in display order.
	 * @param array $groups Field groups in encounter order.
	 *
	 * @throws \InvalidArgumentException If entries are the wrong type.
	 */
	public function __construct(
		private readonly array $fields = array(),
		private readonly array $groups = array(),
	) {
		foreach ( $this->fields as $field ) {
			if ( ! $field instanceof Field ) {
				throw new \InvalidArgumentException( 'FormSchema fields must be Field instances.' );
			}
		}

		foreach ( $this->groups as $group ) {
			if ( ! $group instanceof FieldGroup ) {
				throw new \InvalidArgumentException( 'FormSchema groups must be FieldGroup instances.' );
			}
		}
	}

	/**
	 * All fields in display order.
	 *
	 * @return list<Field>
	 */
	public function fields(): array {
		return $this->fields;
	}

	/**
	 * All groups in encounter order.
	 *
	 * @return list<FieldGroup>
	 */
	public function groups(): array {
		return $this->groups;
	}

	/**
	 * Find a field by its composed path key.
	 *
	 * @param string $path_key Field path key.
	 *
	 * @return Field|null
	 */
	public function field( string $path_key ): ?Field {
		foreach ( $this->fields as $field ) {
			if ( $field->name()->key() === $path_key ) {
				return $field;
			}
		}

		return null;
	}

	/**
	 * Find a group by its composed path key.
	 *
	 * @param string $path_key Group path key.
	 *
	 * @return FieldGroup|null
	 */
	public function group( string $path_key ): ?FieldGroup {
		foreach ( $this->groups as $group ) {
			if ( $group->name()->key() === $path_key ) {
				return $group;
			}
		}

		return null;
	}
}
