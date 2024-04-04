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
import { POST_TYPE } from '../../shared/constants';

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
	'omniform/fieldset',
	'omniform/field',
	'omniform/label',
	'omniform/input',
	'omniform/hidden',
	'omniform/textarea',
	'omniform/select',
	'omniform/select-group',
	'omniform/select-option',
	'omniform/captcha',
	'core/audio',
	'core/block',
	'core/code',
	'core/column',
	'core/columns',
	'core/cover',
	'core/file',
	'core/gallery',
	'core/group',
	'core/heading',
	'core/image',
	'core/list-item',
	'core/list',
	'core/missing',
	'core/paragraph',
	'core/pattern',
	'core/preformatted',
	'core/separator',
	'core/site-logo',
	'core/site-tagline',
	'core/site-title',
	'core/spacer',
	'core/table',
	'core/video',
];

export default function FormInnerBlocks( {
	blockProps,
	formId: id,
	hasInnerBlocks,
} ) {
	const defaultBlockContext = useMemo( () => {
		return { postId: id, postType: POST_TYPE };
	}, [ id ] );

	const [ blocks, onInput, onChange ] = useEntityBlockEditor(
		'postType',
		POST_TYPE,
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
