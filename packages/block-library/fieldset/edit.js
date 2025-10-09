/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { Required } from '../shared/icons';
import { cleanFieldName } from '../shared/utils';
import { useStandaloneFormSettings, useStandardFormSettings } from '../form/utils/hooks';
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes: { fieldLabel, fieldName, isRequired },
	setAttributes,
	clientId,
	context,
} ) => {
	const {
		updateBlockAttributes,
	} = useDispatch( blockEditorStore );

	const { formBlockObject } = useSelect(
		( select ) => {
			const {
				getBlock,
				getBlockParents,
			} = select( blockEditorStore );

			const parentBlocks = getBlockParents( clientId );

			const rootBlock = parentBlocks.find( ( blockId ) => {
				const block = getBlock( blockId );
				return block?.name === 'omniform/form';
			} );

			return {
				formBlockObject: {
					...getBlock( rootBlock ),
					setAttributes: ( value ) => updateBlockAttributes( rootBlock, value ),
				},
			};
		},
		[ clientId, updateBlockAttributes ]
	);

	// Manage the required label.
	const { postId: contextPostId, postType: contextPostType } = context;

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
	} );
	const innerBlockProps = useInnerBlocksProps();

	/**
	 * Toggles the required attribute.
	 */
	const toggleRequired = () =>
		setAttributes( { isRequired: ! isRequired } );

	return (
		<div { ...blockProps } >
			<div className="omniform-field-label">
				<RichText
					ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
					identifier="fieldsetLabel"
					aria-label={ __( 'Legend text', 'omniform' ) }
					placeholder={ __( 'Enter a title to the field…', 'omniform' ) }
					multiple={ false }
					value={ fieldLabel || '' }
					onChange={ ( html ) => ! fieldName || fieldName === cleanFieldName( fieldLabel )
						? setAttributes( { fieldLabel: html, fieldName: cleanFieldName( html ) } )
						: setAttributes( { fieldLabel: html } )
					}
					withoutInteractiveFormatting
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
				/>

				{ isRequired && (
					contextPostId && 'omniform' === contextPostType
						? <StandardFormRequiredLabel clientId={ clientId } formId={ contextPostId } />
						: <StandaloneFormRequiredLabel clientId={ clientId } blockObject={ formBlockObject } />
				) }
			</div>

			<div { ...innerBlockProps } />

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ Required }
						isActive={ isRequired }
						label={ __( 'Required for submission', 'omniform' ) }
						onClick={ toggleRequired }
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Fieldset Settings', 'omniform' ) }>

					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ isRequired }
						onChange={ toggleRequired }
						help={ __( 'Set default \'required\' state for all fields in the group.', 'omniform' ) }
						__nextHasNoMarginBottom
					/>

					<TextControl
						label={ __( 'Name', 'omniform' ) }
						value={ fieldName || '' }
						onChange={ ( newFieldName ) => {
							setAttributes( { fieldName: newFieldName } );
						} }
						onBlur={ () => {
							setAttributes( { fieldName: cleanFieldName( fieldName || fieldLabel ) } );
						} }
						help={ __( 'Name of the fieldset. Defaults to the fieldset\'s label.', 'omniform' ) }
					/>

				</PanelBody>
			</InspectorControls>
		</div>
	);
};

function StandardFormRequiredLabel( { clientId, formId } ) {
	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( formId );

	const requiredLabel = getSetting( 'required_label' ) || '';

	return (
		<RichText
			ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
			className="omniform-field-required"
			placeholder={ __( 'Enter a required field label…', 'omniform' ) }
			value={ requiredLabel }
			onChange={ ( newValue ) => setSetting( 'required_label', newValue ) }
			withoutInteractiveFormatting
			allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			disableLineBreaks
			style={ { gap: requiredLabel ? undefined : '0' } }
		/>
	);
}

function StandaloneFormRequiredLabel( { clientId, blockObject } ) {
	const {
		getSetting,
		setSetting,
	} = useStandaloneFormSettings( blockObject );

	const requiredLabel = getSetting( 'required_label' ) || '';

	return (
		<RichText
			ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
			className="omniform-field-required"
			placeholder={ __( 'Enter a required field label…', 'omniform' ) }
			value={ requiredLabel }
			onChange={ ( newValue ) => setSetting( 'required_label', newValue ) }
			withoutInteractiveFormatting
			allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			disableLineBreaks={ true }
			style={ { gap: requiredLabel ? undefined : '0' } }
		/>
	);
}

export default Edit;
