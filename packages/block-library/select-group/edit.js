/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import {
	Button,
	__experimentalHStack as HStack,
} from '@wordpress/components';
import { chevronRight, chevronDown } from '@wordpress/icons';

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
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option' ],
		template: [
			[ 'omniform/select-option', { label: 'Option One' } ],
			[ 'omniform/select-option', { label: 'Option Two' } ],
			[ 'omniform/select-option', { label: 'Option Three' } ],
		],
	} );

	const [ isOpened, setIsOpened ] = useState( false );

	return (
		<div
			{ ...blockProps }
			className={ classnames( blockProps.className, {
				[ `is-opened` ]: isOpened,
			} ) }
		>
			<HStack
				as="li"
				alignment="left"
			>
				<Button
					isSmall
					icon={ isOpened ? chevronDown : chevronRight }
					onClick={ () => setIsOpened( ! isOpened ) }
				/>
				<RichText
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write the option text…', 'omniform' ) }
					allowedFormats={ [] }
					withoutInteractiveFormatting
					value={ label }
					onChange={ ( html ) => setAttributes( { label: html } ) }
				/>
			</HStack>
			{ isOpened && <ul { ...innerBlockProps } /> }
		</div>
	);
};
export default Edit;
