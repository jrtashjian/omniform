/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
	isSelected,
} ) => {
	const {
		multiple,
		label,
		help,
	} = attributes;

	const blockProps = useBlockProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'inquirywp-field-select' ) }
		>
			<RichText
				className="inquirywp-field-label"
				aria-label={ __( 'Label text', 'inquirywp' ) }
				placeholder={ __( 'Enter a label to the field…', 'inquirywp' ) }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>

			<select className="inquirywp-field-control" multiple={ multiple }>
				<option value="1">One</option>
				<option value="2">Two</option>
				<option value="3">Three</option>
			</select>

			{ ( isSelected || help ) && (
				<RichText
					className="inquirywp-field-support"
					tagName="p"
					aria-label={ __( 'Help text', 'inquirywp' ) }
					placeholder={ __( 'Write a help text…', 'inquirywp' ) }
					withoutInteractiveFormatting
					value={ help }
					onChange={ ( html ) => setAttributes( { help: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;
