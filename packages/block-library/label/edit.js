/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { cleanFieldName } from '../shared/utils';
import { useStandaloneFormSettings, useStandardFormSettings } from '../form/utils/hooks';
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes: { fieldLabel },
	clientId,
	context,
} ) => {
	const {
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const {
		updateBlockAttributes,
	} = useDispatch( blockEditorStore );

	const { postId: contextPostId, postType: contextPostType } = context;

	const contextFieldName = context[ 'omniform/fieldName' ] || '';
	const contextFieldLabel = context[ 'omniform/fieldLabel' ] || '';

	/**
	 * Updates the field label of the parent block.
	 *
	 * @param {string} value The new field label.
	 */
	const updateLabel = ( value ) => {
		const cleanLabel = cleanFieldName( contextFieldLabel );

		if ( ! contextFieldName || contextFieldName === cleanLabel ) {
			updateBlockAttributes(
				getBlockRootClientId( clientId ),
				{
					fieldLabel: value,
					fieldName: cleanFieldName( value ),
				}
			);
		} else {
			updateBlockAttributes(
				getBlockRootClientId( clientId ),
				{ fieldLabel: value }
			);
		}
	};

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
	} );

	return (
		<div { ...blockProps }>
			<RichText
				ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
				placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
				value={ fieldLabel || contextFieldLabel }
				onChange={ updateLabel }
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
				disableLineBreaks
			/>

			{ context[ 'omniform/fieldIsRequired' ] && (
				contextPostId && 'omniform' === contextPostType
					? <StandardFormRequiredLabel clientId={ clientId } formId={ contextPostId } />
					: <StandaloneFormRequiredLabel clientId={ clientId } />
			) }
		</div>
	);
};

function StandardFormRequiredLabel( { clientId, formId } ) {
	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( formId );

	return (
		<RichText
			ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
			className="omniform-field-required"
			placeholder={ __( 'Enter a required field label…', 'omniform' ) }
			value={ getSetting( 'required_label' ) || '' }
			onChange={ ( newValue ) => setSetting( 'required_label', newValue ) }
			withoutInteractiveFormatting
			allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			disableLineBreaks
		/>
	);
}

function StandaloneFormRequiredLabel( { clientId } ) {
	// Find the client ID of the parent form block.
	const blockClientId = useSelect(
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

			return rootBlock;
		},
		[ clientId ]
	);

	const {
		getSetting,
		setSetting,
	} = useStandaloneFormSettings( blockClientId );

	return (
		<RichText
			ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
			className="omniform-field-required"
			placeholder={ __( 'Enter a required field label…', 'omniform' ) }
			value={ getSetting( 'required_label' ) || '' }
			onChange={ ( newValue ) => setSetting( 'required_label', newValue ) }
			withoutInteractiveFormatting
			allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			disableLineBreaks={ true }
		/>
	);
}

export default Edit;
