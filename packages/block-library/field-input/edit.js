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
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';
import { Required } from '../shared/icons';
import FieldInspectorControls from '../shared/inspector-controls';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
		context,
	} = props;
	const {
		fieldPlaceholder,
		fieldType,
		fieldValue,
		isRequired,
		isLabelHidden,
	} = attributes;

	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );
	const isHiddenInput = fieldType === 'hidden';
	const isGrouped = !! context[ 'omniform/fieldGroupName' ];

	const blockProps = useBlockProps();

	const richTextPlaceholder = isHiddenInput
		? __( 'Enter a value…', 'omniform' )
		: __( 'Enter a placeholder…', 'omniform' );
	const richTextOnChange = ( html ) => isHiddenInput
		? setAttributes( { fieldValue: html } )
		: setAttributes( { fieldPlaceholder: html } );

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				`omniform-field-${ fieldType }`,
				{ [ `field-required` ]: isRequired }
			) }
		>
			{ ( ! isLabelHidden || isSelected ) && (
				<FormLabel
					originBlockProps={ props }
					isRadioInput={ 'radio' === fieldType }
					isGrouped={ isGrouped }
					isOptionInput={ isOptionInput }
				/>
			) }

			{ isTextInput && (
				<RichText
					identifier="fieldControl"
					className="omniform-field-control"
					aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
					placeholder={
						( fieldPlaceholder || fieldValue || ! isSelected )
							? undefined
							: richTextPlaceholder
					}
					value={ isHiddenInput ? fieldValue : fieldPlaceholder }
					onChange={ richTextOnChange }
					withoutInteractiveFormatting
					allowedFormats={ [] }
					// When hitting enter, place a new insertion point. This makes adding field a lot easier.
					onSplit={ ( _value, isOriginal ) => {
						const block = isOriginal
							? createBlock( props.name, props.attributes )
							: createBlock( getDefaultBlockName() );

						if ( isOriginal ) {
							block.clientId = props.clientId;
						}

						return block;
					} }
					onReplace={ props.onReplace }
				/>
			) }

			{ isOptionInput && (
				<div className="omniform-field-control" />
			) }

			{ ! isTextInput && ! isOptionInput && (
				<input
					className="omniform-field-control"
					type={ fieldType }
					disabled
				/>
			) }

			<BlockControls>
				<ToolbarGroup>
					{ ! isHiddenInput && (
						<ToolbarButton
							icon={ Required }
							isActive={ isRequired }
							label={ __( 'Required field', 'omniform' ) }
							onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>

			<FieldInspectorControls
				originBlockProps={ props }
				showRequiredControl
				showLabelControl={ ! ( isOptionInput || isHiddenInput ) }
			/>
		</div>
	);
};
export default Edit;
