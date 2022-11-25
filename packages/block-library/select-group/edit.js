/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
	} = props;
	const {
		label,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'omniform-select-group',
	} );

	const innerBlockProps = useInnerBlocksProps( {
		ref: blockProps.ref,
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [
			[ 'omniform/select-option', { label: 'Option One' } ],
			[ 'omniform/select-option', { label: 'Option Two' } ],
			[ 'omniform/select-option', { label: 'Option Three' } ],
		],
	} );

	return (
		<div { ...blockProps }>
			<RichText
				tagName="li"
				aria-label={ __( 'Help text', 'omniform' ) }
				placeholder={ __( 'Write the option text…', 'omniform' ) }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>
			<ul { ...innerBlockProps } />
		</div>
	);
};
export default Edit;
