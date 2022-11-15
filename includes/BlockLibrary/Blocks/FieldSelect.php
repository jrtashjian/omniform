<?php
/**
 * The FieldSelect block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

/**
 * The FieldSelect block class.
 */
class FieldSelect extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) {
		parent::renderBlock( $attributes, $content );

		if ( empty( $this->renderFieldLabel() ) ) {
			return '';
		}

		$field_attributes = array(
			'id'   => esc_attr( $this->field_name ),
			'name' => esc_attr( $this->field_name ),
		);

		if ( ! empty( $attributes['multiple'] ) ) {
			$field_attributes['multiple'] = 'multiple';
			$field_attributes['name']     = $field_attributes['name'] . '[]';
		}

		$field_attributes = array_filter(
			array_map(
				function( $attr, $value ) {
					return $attr . '="' . $value . '"';
				},
				array_keys( $field_attributes ),
				$field_attributes
			)
		);

		$field_control = sprintf(
			'<select class="inquirywp-field-control" %s><option value="One">One</option><option value="Two">Two</option><option value="Three">Three</option></select>',
			implode( ' ', $field_attributes )
		);

		return sprintf(
			'<div class="wp-block-inquirywp-%1$s inquirywp-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel() . $field_control . $this->renderFieldHelpText() . $content
		);
	}
}
