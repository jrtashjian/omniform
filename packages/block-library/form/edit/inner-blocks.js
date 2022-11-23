/**
 * WordPress dependencies
 */
import { useEntityBlockEditor } from '@wordpress/core-data';
import {
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { FORM_POST_TYPE } from '../../shared/constants';

/**
 * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
 * The contents of the array should never change.
 * The array should contain the name of each block that is allowed.
 *
 * @constant
 * @type {string[]}
 */
const ALLOWED_BLOCKS = [
	'core/group',
	'core/heading',
	'core/image',
	'core/paragraph',
	'core/spacer',
	'omniform/button-submit',
	'omniform/field-input',
	'omniform/field-select',
	'omniform/field-textarea',
	'omniform/fieldset',
];

export default function FormInnerBlocks( {
	blockProps,
	formId: id,
	hasInnerBlocks,
} ) {
	const [ blocks, onInput, onChange ] = useEntityBlockEditor(
		'postType',
		FORM_POST_TYPE,
		{ id }
	);

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		value: blocks,
		onInput,
		onChange,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	return <div { ...innerBlockProps } />;
}
