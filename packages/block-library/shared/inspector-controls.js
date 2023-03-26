/**
 * External dependencies
 */
import { kebabCase } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

export default function FieldInspectorControls( {
	originBlockProps,
	showRequiredControl,
	showLabelControl,
} ) {
	const {
		attributes,
		setAttributes,
	} = originBlockProps;

	const onLabelHiddenChange = ( enable ) => setAttributes( {
		isLabelHidden: enable,
		fieldPlaceholder: enable ? attributes.fieldLabel : '',
	} );

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Field Settings', 'omniform' ) }>

				{ showRequiredControl && (
					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ attributes.isRequired }
						onChange={ () => {
							setAttributes( { isRequired: ! attributes.isRequired } );
						} }
					/>
				) }

				{ showLabelControl && (
					<ToggleControl
						label={ __( 'Hidden label', 'omniform' ) }
						checked={ attributes.isLabelHidden }
						onChange={ () => {
							onLabelHiddenChange( ! attributes.isLabelHidden );
						} }
					/>
				) }

				<TextControl
					label={ __( 'Field Name', 'omniform' ) }
					value={ attributes.fieldName }
					onChange={ ( newFieldName ) => {
						setAttributes( {
							fieldName: kebabCase( newFieldName ? newFieldName : attributes.fieldLabel ),
						} );
					} }
					help={ __( 'The fieldName.', 'omniform' ) }
				/>

				<TextControl
					label={ __( 'Field Value', 'omniform' ) }
					value={ attributes.fieldValue }
					onChange={ ( fieldValue ) => {
						setAttributes( { fieldValue } );
					} }
					help={ __( 'The fieldValue.', 'omniform' ) }
				/>

			</PanelBody>
		</InspectorControls>
	);
}
