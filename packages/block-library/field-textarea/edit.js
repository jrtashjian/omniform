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
import {
	ToolbarButton,
	ToolbarGroup,
	ResizableBox,
} from '@wordpress/components';

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
		isRequired,
		isLabelHidden,
		height,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, {
				[ `field-required` ]: isRequired,
			} ) }
		>
			{ ( ! isLabelHidden || isSelected ) && (
				<FormLabel originBlockProps={ props } />
			) }

			<ResizableBox
				className="omniform-field-control"
				size={ { height: height || 'auto' } }
				showHandle={ isSelected }
				enable={ { bottom: true } }
				onResizeStop={ ( _event, _direction, elt ) => {
					setAttributes( { height: parseInt( elt.style.height ) } );
				} }
			>
				<div>
					<RichText
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
				</div>
			</ResizableBox>

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ Required }
						isActive={
							typeof isRequired === 'undefined'
								? context[ 'omniform/fieldGroupRequired' ]
								: isRequired
						}
						label={ __( 'Required field', 'omniform' ) }
						onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
					/>
				</ToolbarGroup>
			</BlockControls>

			<FieldInspectorControls
				originBlockProps={ props }
				showRequiredControl
				showLabelControl
			/>
		</div>
	);
};
export default Edit;
