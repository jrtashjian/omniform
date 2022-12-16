<?php
/**
 * The FieldSelect block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The FieldSelect block class.
 */
class FieldSelect extends BaseFieldBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderField() {
		if ( empty( $this->renderFieldLabel() ) ) {
			return '';
		}

		$field_attributes = array(
			'id'   => esc_attr( $this->field_name ),
			'name' => esc_attr( $this->field_name ),
		);

		if ( ! empty( $this->attributes['isMultiple'] ) ) {
			$field_attributes['multiple'] = 'isMultiple';
			$field_attributes['name']     = $field_attributes['name'] . '[]';

		}

		if ( ! empty( $this->attributes['height'] ) ) {
			$field_attributes['style'] = 'height: ' . $this->attributes['height'] . 'px;';
		}

		// Nest form data within a fieldset.
		if ( ! empty( $this->instance->context['omniform/fieldGroupName'] ) ) {
			$field_attributes['name'] = $this->instance->context['omniform/fieldGroupName'] . '[' . sanitize_title( $field_attributes['name'] ) . ']';
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
		if ( ! empty( $this->attributes['fieldPlaceholder'] ) ) {
			$placeholder_option = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->attributes['fieldPlaceholder'] )
			);
		}

		$field_control = sprintf(
			'<select class="omniform-field-control" %s>%s</select>',
			implode( ' ', $field_attributes ),
			$placeholder_option . $this->renderContent()
		);

		$classes = array(
			'wp-block-omniform-' . $this->blockTypeName(),
			'omniform-' . $this->blockTypeName(),
			$this->getColorClasses( $this->attributes ),
		);

		return sprintf(
			'<div class="%s" %s>%s</div>',
			esc_attr( implode( ' ', $classes ) ),
			$this->getColorStyles( $this->attributes ),
			$this->renderFieldLabel() . $field_control . $this->renderFieldError()
		);
	}
}
