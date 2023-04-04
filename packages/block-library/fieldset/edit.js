/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { cleanForSlug } from '@wordpress/url';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect, useDispatch } from '@wordpress/data';

const Edit = ( {
	attributes: { fieldLabel, fieldName, isRequired },
	setAttributes,
	clientId,
	context,
} ) => {
	// Manage the required label.
	const { postId: contextPostId, postType: contextPostType } = context;

	const [ meta, setMeta ] = useEntityProp( 'postType', contextPostType, 'meta', contextPostId );

	const metaRequiredLabel = meta?.required_label;
	const updateMetaRequiredLabel = ( newValue ) => {
		setMeta( { ...meta, required_label: newValue } );
	};

	const blockProps = useBlockProps();
	const innerBlockProps = useInnerBlocksProps();

	// Update all child field blocks with a new required setting.
	const { updateBlockAttributes } = useDispatch( blockEditorStore );
	const { getBlockOrder } = useSelect( ( select ) => select( blockEditorStore ), [] );

	const updateRequired = ( newIsRequired ) => {
		setAttributes( { isRequired: newIsRequired } );

		// Update all child field Blocks to match.
		const innerBlockClientIds = getBlockOrder( clientId );
		innerBlockClientIds.forEach( ( innerBlockClientId ) => {
			updateBlockAttributes( innerBlockClientId, {
				isRequired: newIsRequired,
			} );
		} );
	};

	return (
		<div { ...blockProps } >
			<div className="omniform-field-label">
				<RichText
					identifier="fieldsetLabel"
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
				{ isRequired && (
					<RichText
						identifier="requiredLabel"
						tagName="span"
						className="omniform-field-required"
						placeholder={ __( 'Enter a required field label…', 'omniform' ) }
						value={ metaRequiredLabel }
						onChange={ updateMetaRequiredLabel }
						withoutInteractiveFormatting
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
					/>
				) }
			</div>

			<div { ...innerBlockProps } />

			<InspectorControls>
				<PanelBody title={ __( 'Fieldset Settings', 'omniform' ) }>

					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ isRequired }
						onChange={ () => {
							updateRequired( ! isRequired );
							// setAttributes( { isRequired: ! isRequired } );
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
							setAttributes( { fieldName: cleanForSlug( ( fieldName || fieldLabel ).replace( /(<([^>]+)>)/gi, '' ) ) } );
						} }
						help={ __( 'Name of the fieldset. Defaults to the fieldset\s label.', 'omniform' ) }
					/>

				</PanelBody>
			</InspectorControls>
		</div>
	);
};
export default Edit;
