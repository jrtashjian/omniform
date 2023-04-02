/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { cleanForSlug } from '@wordpress/url';

const Edit = ( {
	attributes: { fieldLabel, fieldName, isRequired },
	setAttributes,
} ) => {
	const blockProps = useBlockProps();

	const innerBlockOptions = {
		__experimentalCaptureToolbars: true,
		templateInsertUpdatesSelection: true,
		templateLock: 'insert',
		template: [
			[ 'omniform/label' ],
			[ 'omniform/input' ],
		],
	};

	const innerBlockProps = useInnerBlocksProps( blockProps, innerBlockOptions );

	return (
		<>
			<div { ...innerBlockProps } />
			<InspectorControls>
				<PanelBody title={ __( 'Field Settings', 'omniform' ) }>

					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ isRequired }
						onChange={ () => {
							setAttributes( { isRequired: ! isRequired } );
						} }
						help={ __( 'A value is required or must be check for the form to be submittable.', 'omniform' ) }
					/>

					<TextControl
						label={ __( 'Name', 'omniform' ) }
						value={ fieldName }
						onChange={ ( newFieldName ) => {
							setAttributes( { fieldName: newFieldName } );
						} }
						onBlur={ () => {
							setAttributes( { fieldName: cleanForSlug( fieldName || fieldLabel ) } );
						} }
						help={ __( 'Name of the form control. Defaults to the label.', 'omniform' ) }
					/>

				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default Edit;
