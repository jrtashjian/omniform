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
import { POST_TYPE, ALLOWED_BLOCKS } from '../../shared/constants';

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
