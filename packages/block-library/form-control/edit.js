/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<label htmlFor="textInput" className="form-label">textInput</label>
			<input type="text" className="form-control" id="textInput" aria-describedby="textInputHelp" />
			<div id="textInputHelp" className="form-text">textInputHelp</div>
		</div>
	);
};
export default Edit;
