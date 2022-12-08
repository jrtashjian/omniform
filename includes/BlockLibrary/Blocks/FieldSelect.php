<?php
/**
 * The FieldSelect block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldSelect block class.
 */
class FieldSelect extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		parent::renderBlock( $attributes, $content, $block );

		if ( empty( $this->renderFieldLabel() ) ) {
			return '';
		}

		$field_attributes = array(
			'id'   => esc_attr( $this->field_name ),
			'name' => esc_attr( $this->field_name ),
		);

		if ( ! empty( $attributes['isMultiple'] ) ) {
			$field_attributes['multiple'] = 'isMultiple';
			$field_attributes['name']     = $field_attributes['name'] . '[]';

		}
		if ( ! empty( $attributes['height'] ) ) {
			$field_attributes['style'] = 'height: ' . $attributes['height'] . 'px;';
		}

		// Nest form data within a fieldset.
		if ( ! empty( $block->context['omniform/fieldGroupName'] ) ) {
			$field_attributes['name'] = $block->context['omniform/fieldGroupName'] . '[' . sanitize_title( $field_attributes['name'] ) . ']';
		}

		// Stitch together the input's attributes.
		$field_attributes = array_map(
			function( $attr, $val ) {
				return sprintf( '%1$s="%2$s"', esc_attr( $attr ), esc_attr( $val ) );
			},
			array_keys( $field_attributes ),
			$field_attributes
		);

		$placeholder_option = '';
		if ( ! empty( $attributes['fieldPlaceholder'] ) ) {
			$placeholder_option = sprintf(
				'<option value="">%s</option>',
				esc_attr( $attributes['fieldPlaceholder'] )
			);
		}

		$field_control = sprintf(
			'<select class="omniform-field-control" %s>%s</select>',
			implode( ' ', $field_attributes ),
			$placeholder_option . do_blocks( $content )
		);

		return sprintf(
			'<div class="wp-block-omniform-%1$s omniform-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel() . $field_control . $this->renderFieldError()
		);
	}
}
