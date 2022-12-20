/**
 * WordPress dependencies
 */
import { useEntityBlockEditor } from '@wordpress/core-data';
import {
	InnerBlocks,
	useInnerBlocksProps,
	BlockContextProvider,
} from '@wordpress/block-editor';
import { useMemo } from '@wordpress/element';

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
	'omniform/button',
	'omniform/field-input',
	'omniform/field-select',
	'omniform/select-option',
	'omniform/select-group',
	'omniform/field-textarea',
	'omniform/fieldset',
	'core/paragraph',
	'core/image',
	'core/heading',
	'core/gallery',
	'core/list',
	'core/list-item',
	'core/quote',
	'core/audio',
	'core/code',
	'core/column',
	'core/columns',
	'core/cover',
	'core/file',
	'core/group',
	'core/media-text',
	'core/missing',
	'core/pattern',
	'core/preformatted',
	'core/pullquote',
	'core/block',
	'core/separator',
	'core/spacer',
	'core/table',
	'core/verse',
	'core/video',
	'core/site-logo',
	'core/site-title',
	'core/site-tagline',
];

export default function FormInnerBlocks( {
	blockProps,
	formId: id,
	hasInnerBlocks,
} ) {
	const defaultBlockContext = useMemo( () => {
		return { postId: id, postType: FORM_POST_TYPE };
	}, [ id ] );

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

	return (
		<BlockContextProvider value={ defaultBlockContext }>
			<div { ...innerBlockProps } />
		</BlockContextProvider>
	);
}
