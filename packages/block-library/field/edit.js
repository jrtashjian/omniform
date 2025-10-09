/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	BlockControls,
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { Required } from '../shared/icons';
import { cleanFieldName } from '../shared/utils';

const Edit = ( {
	attributes: { fieldLabel, fieldName, isRequired },
	setAttributes,
	clientId,
} ) => {
	const {
		labelBlock,
		inputBlock,
		hasLabel,
		canHideLabel,
	} = useSelect(
		( select ) => {
			const { getBlocks } = select( blockEditorStore );
			const blocks = getBlocks( clientId );

			const _labelBlock = blocks.find( ( block ) => block.name === 'omniform/label' );
			const _inputBlock = blocks.find( ( block ) => [ 'omniform/input', 'omniform/textarea', 'omniform/select' ].includes( block.name ) );

			return {
				labelBlock: _labelBlock,
				inputBlock: _inputBlock,
				hasLabel: !! _labelBlock,
				canHideLabel: _inputBlock && ! ( [ 'checkbox', 'radio' ].includes( _inputBlock.attributes?.fieldType ) || _inputBlock.attributes?.isMultiple ),
			};
		},
		[ clientId ]
	);

	const {
		insertBlock,
		removeBlock,
		updateBlockAttributes,
		updateBlockListSettings,
	} = useDispatch( blockEditorStore );

	const blockProps = useBlockProps();

	const innerBlockOptions = {
		__experimentalCaptureToolbars: true,
		templateLock: 'insert',
		template: [
			[ 'omniform/label' ],
			[ 'omniform/input' ],
		],
	};

	const innerBlockProps = useInnerBlocksProps( blockProps, innerBlockOptions );

	/**
	 * Updates the field label of the parent block.
	 *
	 * @param {string} value The new field label.
	 */
	const updateLabel = ( value ) => {
		const cleanLabel = cleanFieldName( fieldLabel );

		if ( ! fieldName || fieldName === cleanLabel ) {
			setAttributes( {
				fieldLabel: value,
				fieldName: cleanFieldName( value ),
			} );
		} else {
			setAttributes( { fieldLabel: value } );
		}
	};

	/**
	 * Toggles the label block.
	 */
	const toggleLabel = () => {
		if ( hasLabel ) {
			// Remove the label block.
			updateBlockAttributes( [ labelBlock.clientId ], { lock: { remove: false } } );
			removeBlock( labelBlock.clientId, false );

			// Update the input block's placeholder.
			updateBlockAttributes(
				[ inputBlock.clientId ],
				{ fieldPlaceholder: fieldLabel.replace( /(<([^>]+)>)/gi, '' ) }
			);
		} else {
			// Add the label block.
			updateBlockListSettings( clientId, { templateLock: false } );
			insertBlock( createBlock( 'omniform/label' ), 0, clientId, false );
			updateBlockListSettings( clientId, { templateLock: true } );

			// Remove the input block's placeholder.
			updateBlockAttributes(
				[ inputBlock.clientId ],
				{ fieldPlaceholder: undefined }
			);
		}
	};

	/**
	 * Toggles the required attribute.
	 */
	const toggleRequired = () =>
		setAttributes( { isRequired: ! isRequired } );

	return (
		<>
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
				<PanelBody title={ __( 'Field Settings', 'omniform' ) }>

					<ToggleControl
						label={ __( 'Required for submission', 'omniform' ) }
						checked={ isRequired }
						onChange={ toggleRequired }
						help={ __( 'A value is required or must be check for the form to be submittable.', 'omniform' ) }
						__nextHasNoMarginBottom
					/>

					{ canHideLabel && (
						<ToggleControl
							label={ __( 'Hidden label', 'omniform' ) }
							checked={ ! hasLabel }
							onChange={ toggleLabel }
							help={ __( 'Hide the field\'s label, current label becomes the field\'s placeholder.', 'omniform' ) }
							__nextHasNoMarginBottom
						/>
					) }

					{ ! hasLabel && (
						<TextControl
							label={ __( 'Label', 'omniform' ) }
							value={ fieldLabel }
							onChange={ updateLabel }
							help={ __( 'Label for the form control.', 'omniform' ) }
						/>
					) }

					<TextControl
						label={ __( 'Name', 'omniform' ) }
						value={ fieldName }
						onChange={ ( newFieldName ) => {
							setAttributes( { fieldName: newFieldName } );
						} }
						onBlur={ () => {
							setAttributes( { fieldName: cleanFieldName( fieldName || fieldLabel ) } );
						} }
						help={ __( 'Name of the form control. Defaults to the label.', 'omniform' ) }
					/>

				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default Edit;
