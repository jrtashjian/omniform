<?php
/**
 * Form response (submitted snapshot).
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable record of a form schema and its submitted values at a point in time.
 *
 * This is the domain model for an omniform_response. WordPress persistence
 * (CPT load/save) lives in a repository adapter, not here.
 */
final class Response {
	public const VERSION = 1;

	/**
	 * @param FormSchema $schema     Field catalog at submit time.
	 * @param Submission $submission Submitted values.
	 * @param int        $version    Schema/payload version for storage evolution.
	 *
	 * @throws \InvalidArgumentException If version is not positive.
	 */
	public function __construct(
		private readonly FormSchema $schema,
		private readonly Submission $submission,
		private readonly int $version = self::VERSION,
	) {
		if ( $this->version < 1 ) {
			throw new \InvalidArgumentException( 'Response version must be a positive integer.' );
		}
	}

	/**
	 * Payload version.
	 */
	public function version(): int {
		return $this->version;
	}

	/**
	 * Form schema frozen at submit time.
	 */
	public function schema(): FormSchema {
		return $this->schema;
	}

	/**
	 * Submitted values.
	 */
	public function submission(): Submission {
		return $this->submission;
	}

	/**
	 * @return array{version: int, schema: array, submission: array}
	 */
	public function to_array(): array {
		return array(
			'version'    => $this->version,
			'schema'     => $this->schema_to_array( $this->schema ),
			'submission' => $this->submission->to_array(),
		);
	}

	/**
	 * @param array<string, mixed> $data Serialized response.
	 *
	 * @throws \InvalidArgumentException If the payload is invalid.
	 */
	public static function from_array( array $data ): self {
		$version = (int) ( $data['version'] ?? self::VERSION );

		return new self(
			schema: self::schema_from_array( $data['schema'] ?? array() ),
			submission: Submission::from_array( $data['submission'] ?? array() ),
			version: $version,
		);
	}

	/**
	 * @return array{fields: list<array>, groups: list<array>}
	 */
	private function schema_to_array( FormSchema $schema ): array {
		return array(
			'fields' => array_map(
				static fn( Field $field ): array => array(
					'name'  => $field->name()->key(),
					'label' => $field->label(),
					'type'  => $field->type(),
					'rules' => $field->rules(),
				),
				$schema->fields()
			),
			'groups' => array_map(
				static fn( FieldGroup $group ): array => array(
					'name'         => $group->name()->key(),
					'label'        => $group->label(),
					'rules'        => $group->rules(),
					'choice_group' => $group->is_choice_group(),
				),
				$schema->groups()
			),
		);
	}

	/**
	 * @param array<string, mixed> $data Serialized schema.
	 */
	private static function schema_from_array( array $data ): FormSchema {
		$fields = array();
		foreach ( $data['fields'] ?? array() as $row ) {
			if ( ! is_array( $row ) ) {
				throw new \InvalidArgumentException( 'Response schema fields must be arrays.' );
			}

			$fields[] = new Field(
				name: FieldPath::from_segments( explode( '.', (string) $row['name'] ) ),
				label: (string) $row['label'],
				type: (string) $row['type'],
				rules: self::string_list( $row['rules'] ?? array() ),
			);
		}

		$groups = array();
		foreach ( $data['groups'] ?? array() as $row ) {
			if ( ! is_array( $row ) ) {
				throw new \InvalidArgumentException( 'Response schema groups must be arrays.' );
			}

			$groups[] = new FieldGroup(
				name: FieldPath::from_segments( explode( '.', (string) $row['name'] ) ),
				label: (string) $row['label'],
				rules: self::string_list( $row['rules'] ?? array() ),
				choice_group: (bool) ( $row['choice_group'] ?? false ),
			);
		}

		return new FormSchema( $fields, $groups );
	}

	/**
	 * @param mixed $rules Raw rules list.
	 * @return list<string>
	 */
	private static function string_list( mixed $rules ): array {
		if ( ! is_array( $rules ) ) {
			return array();
		}

		$list = array();
		foreach ( $rules as $rule ) {
			if ( ! is_string( $rule ) || '' === $rule ) {
				throw new \InvalidArgumentException( 'Response rules must be non-empty strings.' );
			}
			$list[] = $rule;
		}

		return $list;
	}
}
