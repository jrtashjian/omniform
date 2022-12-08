/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';
import { Required } from '../shared/icons';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
	} = props;
	const {
		fieldPlaceholder,
		isRequired,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, {
				[ `field-required` ]: isRequired,
			} ) }
		>
			<FormLabel originBlockProps={ props } />

			<RichText
				identifier="fieldControl"
				className="omniform-field-control"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( fieldPlaceholder || ! isSelected )
						? undefined
						: __( 'Enter a placeholder…', 'omniform' )
				}
				allowedFormats={ [] }
				withoutInteractiveFormatting
				value={ fieldPlaceholder }
				onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
			/>

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ Required }
						isActive={ isRequired }
						label={ __( 'Required field', 'omniform' ) }
						onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
					/>
				</ToolbarGroup>
			</BlockControls>
		</div>
	);
};
export default Edit;
