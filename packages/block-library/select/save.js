/**
 * WordPress dependencies
 */
import { useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	return useInnerBlocksProps.save( useBlockProps.save() ).children;
};
export default Save;
