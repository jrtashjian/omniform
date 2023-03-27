/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { cleanForSlug } from '@wordpress/url';

const Edit = ( {
	attributes,
	setAttributes,
	__unstableLayoutClassNames: layoutClassNames,
} ) => {
	const {
		fieldLabel,
		fieldName,
	} = attributes;

	const blockProps = useBlockProps( { className: layoutClassNames } );

	const innerBlockProps = useInnerBlocksProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-fieldset' ) }
		>
			<RichText
				className="omniform-field-label"
				aria-label={ __( 'Legend text', 'omniform' ) }
				placeholder={ __( 'Enter a title to the field…', 'omniform' ) }
				multiple={ false }
				value={ fieldLabel }
				onChange={ ( html ) => ! fieldName || fieldName === cleanForSlug( fieldLabel.replace( /(<([^>]+)>)/gi, '' ) )
					? setAttributes( { fieldLabel: html, fieldName: cleanForSlug( html.replace( /(<([^>]+)>)/gi, '' ) ) } )
					: setAttributes( { fieldLabel: html } )
				}
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			/>
			{ innerBlockProps.children }

			<InspectorControls>
				<PanelBody title={ __( 'Group Settings', 'omniform' ) }>
					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ attributes.isRequired }
						onChange={ () => {
							setAttributes( { isRequired: ! attributes.isRequired } );
						} }
						help={ __( 'Set default \'required\' state for all fields in the group.', 'omniform' ) }
					/>

					<TextControl
						label={ __( 'Name', 'omniform' ) }
						value={ fieldName }
						onChange={ ( newFieldName ) => {
							setAttributes( { fieldName: newFieldName } );
						} }
						onBlur={ () => {
							setAttributes( { fieldName: cleanForSlug( fieldName ) } );
						} }
						help={ __( 'Name of the fieldset. Defaults to the fieldset\'s label.', 'omniform' ) }
					/>
				</PanelBody>
			</InspectorControls>
		</div>
	);
};
export default Edit;
