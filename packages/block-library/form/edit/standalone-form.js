/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

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
	'omniform/response-notification',
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

const TEMPLATE = [
	[ 'core/group', {
		tagName: 'div',
		align: 'full',
		style: {
			spacing: {
				padding: {
					top: '5em',
					bottom: '5em',
					left: '5em',
					right: '5em',
				},
			},
		},
		layout: {
			type: 'constrained',
		},
	}, [
		[ 'omniform/response-notification', {
			messageType: 'success',
			messageContent: 'Success! Your submission has been completed.',
			style: {
				border: {
					left: {
						color: 'var(--wp--preset--color--vivid-green-cyan,#00d084)',
						width: '6px',
					},
				},
				spacing: {
					padding: {
						top: '0.5em',
						bottom: '0.5em',
						left: '1.5em',
						right: '1.5em',
					},
				},
			},
		}, [] ],
		[ 'omniform/response-notification', {
			messageType: 'error',
			messageContent: 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.',
			style: {
				border: {
					left: {
						color: 'var(--wp--preset--color--vivid-red,#cf2e2e)',
						width: '6px',
					},
				},
				spacing: {
					padding: {
						top: '0.5em',
						bottom: '0.5em',
						left: '1.5em',
						right: '1.5em',
					},
				},
			},
		}, [] ],
		[ 'core/paragraph', {
			content: "If you have any questions or comments, or if you'd like to work with me or collaborate on a project, please don't hesitate to get in touch. I look forward to hearing from you!",
			dropCap: false,
		}, [] ],
		[ 'omniform/field', {
			fieldLabel: 'Your email address',
			fieldName: 'your-email-address',
		}, [
			[ 'omniform/label', {}, [] ],
			[ 'omniform/input', {}, [] ],
		] ],
		[ 'omniform/field', {
			fieldLabel: 'Your message',
			fieldName: 'your-message',
		}, [
			[ 'omniform/label', {}, [] ],
			[ 'omniform/textarea', {
				style: {
					dimensions: {
						minHeight: '230px',
					},
				},
			}, [] ],
		] ],
		[ 'core/group', {
			tagName: 'div',
			layout: {
				type: 'flex',
				flexWrap: 'nowrap',
			},
		}, [
			[ 'omniform/button', {
				buttonType: 'submit',
				buttonLabel: 'Send Message',
			}, [] ],
		] ],
	] ],
];

export default function StandaloneForm() {
	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container',
	} );

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
	} );

	return ( <div { ...innerBlockProps } /> );
}
