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

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
	} = props;
	const {
		multiple,
		help,
	} = attributes;

	const blockProps = useBlockProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-field-select' ) }
		>
			<FormLabel originBlockProps={ props } />

			<select className="omniform-field-control" multiple={ multiple }>
				<option value="1">One</option>
				<option value="2">Two</option>
				<option value="3">Three</option>
			</select>

			{ ( isSelected || help ) && (
				<RichText
					className="omniform-field-support"
					tagName="p"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write a help text…', 'omniform' ) }
					withoutInteractiveFormatting
					value={ help }
					onChange={ ( html ) => setAttributes( { help: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;
