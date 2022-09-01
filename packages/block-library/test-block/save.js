/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	const blockProps = useBlockProps.save();
	return (
		<div { ...blockProps }>
			{ __( 'Hello, World! (from the frontend)', 'pluginwp' ) }
		</div>
	);
};
export default Save;
