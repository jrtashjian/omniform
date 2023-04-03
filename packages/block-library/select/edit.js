/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes: { fieldPlaceholder, isMultiple },
	setAttributes,
	isSelected,
	clientId,
} ) => {
	const hasSelectedInnerBlock = useSelect(
		( select ) => select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId ]
	);

	const blockProps = useBlockProps( {
		className: classNames( {
			[ `type-multiple` ]: isMultiple,
		} ),
	} );

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		templateLock: false,
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [ [ 'omniform/select-option' ] ],
	} );

	if ( isMultiple ) {
		return (
			<div { ...blockProps }>
				<div { ...innerBlockProps } />
			</div>
		);
	}

	return (
		<div { ...blockProps }>
			{ ! isMultiple && (
				<RichText
					identifier="fieldControl"
					aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
					placeholder={
						( ( fieldPlaceholder || ! isSelected ) && ! hasSelectedInnerBlock )
							? undefined
							: __( 'Enter a placeholder…', 'omniform' )
					}
					value={ fieldPlaceholder }
					onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
					withoutInteractiveFormatting
					allowedFormats={ [] }
				/>
			) }

			{ ( isSelected || hasSelectedInnerBlock || isMultiple ) && (
				<div { ...innerBlockProps } />
			) }
		</div>
	);
};

export default Edit;
