/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	ResizableBox,
} from '@wordpress/components';

const Edit = ( {
	attributes: { fieldPlaceholder, height },
	setAttributes,
	isSelected,
} ) => {
	const blockProps = useBlockProps();

	return (
		<ResizableBox
			size={ { height: height || 'auto' } }
			minHeight="24"
			showHandle={ isSelected }
			enable={ { bottom: true } }
			onResizeStop={ ( _event, _direction, elt ) => {
				setAttributes( { height: parseInt( elt.style.height ) } );
			} }
		>
			<RichText
				{ ...blockProps }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( fieldPlaceholder || ! isSelected )
						? undefined
						: __( 'Enter a placeholder…', 'omniform' )
				}
				value={ fieldPlaceholder }
				onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }
			/>
		</ResizableBox>
	);
};

export default Edit;
