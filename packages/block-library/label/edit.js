/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
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

	// Manage the required label.
	const { postId: contextPostId, postType: contextPostType } = context;

	const [ meta, setMeta ] = useEntityProp( 'postType', contextPostType, 'meta', contextPostId );

	const metaRequiredLabel = meta?.required_label;
	const updateMetaRequiredLabel = ( newValue ) => {
		setMeta( { ...meta, required_label: newValue } );
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
				<RichText
					identifier="requiredLabel"
					className="omniform-field-required"
					placeholder={ __( 'Enter a required field label…', 'omniform' ) }
					value={ metaRequiredLabel }
					onChange={ updateMetaRequiredLabel }
					withoutInteractiveFormatting
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
					disableLineBreaks={ true }
				/>
			) }
		</div>
	);
};

export default Edit;
