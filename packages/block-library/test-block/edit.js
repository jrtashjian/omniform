/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			{ __( 'Hello, World! (from the editor)', 'inquirywp' ) }
		</div>
	);
};
export default Edit;
