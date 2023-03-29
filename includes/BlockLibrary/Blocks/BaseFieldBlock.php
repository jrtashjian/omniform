<?php
/**
 * The BaseFieldBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;
use OmniForm\Plugin\Form;

/**
 * The BaseFieldBlock block class.
 */
abstract class BaseFieldBlock extends BaseBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->getFieldLabel() ) ) {
			return '';
		}

		// Remove block supports classes and styles from wrapper. We'll add them to the control.
		$attributes = \WP_Block_Supports::get_instance()->apply_block_supports();

		$attributes['class'] = array_diff(
			explode( ' ', $attributes['class'] ),
			explode( ' ', $this->getSupportsClasses() ),
		);

		$attributes['class'][] = implode( ' ', $this->getDefaultClasses() );

		if ( key_exists( 'style', $attributes ) ) {
			$attributes['style'] = array_diff(
				explode( ' ', $attributes['style'] ),
				explode( ' ', $this->getSupportsStyles() ),
			);
		}

		$attributes = implode(
			' ',
			array(
				key_exists( 'class', $attributes ) ? $this->getElementAttribute( 'class', $attributes['class'] ) : '',
				key_exists( 'style', $attributes ) ? $this->getElementAttribute( 'style', $attributes['style'] ) : '',
			),
		);

		return sprintf(
			'<div %s>%s</div>',
			$attributes,
			$this->renderLabel() . $this->renderControl()
		);
	}

	/**
	 * Get the fieldLabel.
	 *
	 * @return string
	 */
	public function getFieldLabel() {
		return $this->getBlockAttribute( 'fieldLabel' );
	}

	/**
	 * Get the sanitized fieldName key. Fallback to fieldLabel.
	 *
	 * @return string
	 */
	public function getFieldName() {
		$field_name = $this->getBlockAttribute( 'fieldName' );
		return empty( $field_name )
			? sanitize_title( $this->getFieldLabel() )
			: sanitize_title( $field_name );
	}

	/**
	 * Get the sanitized omniform/fieldGroup key.
	 *
	 * @return string
	 */
	public function getFieldGroupName() {
		return $this->isGrouped()
			? sanitize_title( $this->getBlockContext( 'omniform/fieldGroupName' ) )
			: null;
	}

	/**
	 * Get the sanitized omniform/fieldGroup key.
	 *
	 * @return string
	 */
	public function getFieldGroupLabel() {
		return $this->isGrouped()
			? $this->getBlockContext( 'omniform/fieldGroupLabel' )
			: $this->getFieldGroupName();
	}

	/**
	 * Get the default classes to be applied to the block wrapper.
	 *
	 * @return array
	 */
	public function getDefaultClasses() {
		return array(
			// Apply custom class for each field type.
			empty( $this->getBlockAttribute( 'fieldType' ) )
				? 'omniform-' . $this->blockTypeName()
				: 'omniform-field-' . $this->getBlockAttribute( 'fieldType' ),
		);
	}

	/**
	 * If the fieldGroupName context exists, the field is part of a group.
	 *
	 * @return bool
	 */
	public function isGrouped() {
		return ! empty( $this->getBlockContext( 'omniform/fieldGroupName' ) );
	}

	/**
	 * Does the field require a value?
	 *
	 * @return bool
	 */
	public function isRequired() {
		return ! empty( $this->getBlockAttribute( 'isRequired' ) );
	}

	/**
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderLabel() {
		$form_id = omniform()->get( Form::class )->getId() ?? $this->getBlockContext( 'postId' );

		$allowed_html = array(
			'strong' => array(),
			'em'     => array(),
			'img'    => array(
				'class' => true,
				'style' => true,
				'src'   => true,
				'alt'   => true,
			),
		);

		$label_required = null;

		if ( $this->isRequired() && (
			'radio' !== $this->getBlockAttribute( 'fieldType' ) ||
			( 'radio' === $this->getBlockAttribute( 'fieldType' ) && ! $this->isGrouped() )
		) ) {
			$label_required = sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( get_post_meta( $form_id, 'required_label', true ), $allowed_html )
			);
		}

		$classnames = array_filter(
			array(
				'omniform-field-label',
				! empty( $this->getBlockAttribute( 'isLabelHidden' ) ) ? 'screen-reader-text' : null,
			)
		);

		return sprintf(
			'<label class="%s" for="%s">%s</label>',
			esc_attr( implode( ' ', $classnames ) ),
			esc_attr( $this->getFieldName() ),
			wp_kses( $this->getBlockAttribute( 'fieldLabel' ), $allowed_html ) . $label_required
		);
	}

	/**
	 * Render the input's error text element.
	 *
	 * @return string
	 */
	protected function renderFieldError() {
		$errors = false;
		return empty( $errors ) ? '' : sprintf(
			'<p class="omniform-field-support" style="color:red;">%s</p>',
			wp_kses_post( $errors )
		);
	}

	/**
	 * Generate key="value" attributes for control.
	 *
	 * @return array
	 */
	protected function getControlAttributes() {
		return array_filter(
			array(
				$this->getElementAttribute( 'id', sanitize_title( $this->getFieldName() ) ),
				$this->getElementAttribute( 'name', $this->getControlName() ),
				$this->getElementAttribute( 'class', $this->getSupportsClasses() . ' omniform-field-control' ),
				$this->getElementAttribute( 'style', $this->getSupportsStyles() ),
				$this->isRequired() ? 'required' : '',
			)
		);
	}

	public function getControlNameParts() {
		return $this->isGrouped()
			? array( $this->getFieldGroupName(), $this->getFieldName() )
			: array( $this->getFieldName() );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		$parts = $this->getControlNameParts();

		return 2 === count( $parts )
			? sprintf( '%s[%s]', $parts[0], $parts[1] )
			: $parts[0];
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function getControlValue() {
		return $this->getBlockAttribute( 'fieldValue' );
	}

	protected function getSupports() {
		return array_filter(
			array(
				'colors'  => wp_apply_colors_support( $this->instance->block_type, $this->attributes ),
				'border'  => wp_apply_border_support( $this->instance->block_type, $this->attributes ),
				'spacing' => wp_apply_spacing_support( $this->instance->block_type, $this->attributes ),
			)
		);
	}

	protected function getSupportsClasses() {
		$supports = array_filter(
			$this->getSupports(),
			function( $support ) {
				return ! empty( $support['class'] );
			}
		);

		return implode( ' ', wp_list_pluck( $supports, 'class' ) );
	}

	protected function getSupportsStyles() {
		$supports = array_filter(
			$this->getSupports(),
			function( $support ) {
				return ! empty( $support['style'] );
			}
		);

		return implode( ' ', wp_list_pluck( $supports, 'style' ) );
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function renderControl();
}
