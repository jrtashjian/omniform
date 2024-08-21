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

/**
 * Internal dependencies
 */
import {
	onMerge,
	onRemove,
	onReplace,
	onSplit,
} from '../shared/rich-text-handlers';
import { cleanFieldName } from '../shared/utils';
import { useStandaloneFormSettings, useStandardFormSettings } from '../form/utils/hooks';

const Edit = ( {
	attributes: { fieldLabel },
	clientId,
	context,
} ) => {
	const {
		getBlock,
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const {
		updateBlockAttributes,
	} = useDispatch( blockEditorStore );

	const { formBlockObject } = useSelect(
		( select ) => {
			const {
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
		[ clientId, getBlock, updateBlockAttributes ]
	);

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

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<RichText
				identifier="fieldLabel"
				placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
				value={ fieldLabel || contextFieldLabel }
				onChange={ updateLabel }
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }

				onSplit={ ( ...args ) => onSplit( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
				onReplace={ ( ...args ) => onReplace( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
				onMerge={ ( ...args ) => onMerge( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
				onRemove={ ( ...args ) => onRemove( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
			/>

			{ context[ 'omniform/fieldIsRequired' ] && (
				contextPostId && 'omniform' === contextPostType
					? <StandardFormRequiredLabel formId={ contextPostId } />
					: <StandaloneFormRequiredLabel blockObject={ formBlockObject } />
			) }
		</div>
	);
};

function StandardFormRequiredLabel( { formId } ) {
	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( formId );

	return (
		<RichText
			identifier="requiredLabel"
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

function StandaloneFormRequiredLabel( { blockObject } ) {
	const {
		getSetting,
		setSetting,
	} = useStandaloneFormSettings( blockObject );

	return (
		<RichText
			identifier="requiredLabel"
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
