/**
 * WordPress dependencies
 */
import { useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	return <form { ...useInnerBlocksProps.save( useBlockProps.save() ) } />;
};
export default Save;
