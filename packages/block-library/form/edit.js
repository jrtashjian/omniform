/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps();
	const innerBlockProps = useInnerBlocksProps();

	return (
		<form { ...blockProps }>
			<div { ...innerBlockProps } />
			<button
				className="form-submit-button"
				type="submit"
				disabled
			>
				{ __( 'Submit', 'inquirywp' ) }
			</button>
		</form>
	);
};
export default Edit;
