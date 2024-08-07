/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	useInnerBlocksProps,
	Warning,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

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
		layout: {
			type: 'constrained',
		},
	}, [
		[ 'omniform/response-notification', {
			messageType: 'success',
			messageContent: __( 'Success! Your submission has been completed.', 'omniform' ),
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
			messageContent: __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ),
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
			content: __( "If you have any questions or comments, or if you'd like to work with me or collaborate on a project, please don't hesitate to get in touch. I look forward to hearing from you!", 'omniform' ),
			dropCap: false,
		}, [] ],
		[ 'omniform/field', {
			fieldLabel: __( 'Your email address', 'omniform' ),
			fieldName: 'your-email-address',
		}, [
			[ 'omniform/label', {}, [] ],
			[ 'omniform/input', {}, [] ],
		] ],
		[ 'omniform/field', {
			fieldLabel: __( 'Your message', 'omniform' ),
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
				buttonLabel: __( 'Send Message', 'omniform' ),
			}, [] ],
		] ],
	] ],
];

export default function StandaloneForm( {
	name,
	clientId,
} ) {
	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container',
	} );

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
	} );

	const isNested = useSelect(
		( select ) => {
			const {
				getBlockParents,
				getBlock,
			} = select( blockEditorStore );

			const parentBlocks = getBlockParents( clientId );
			const rootBlock = getBlock( parentBlocks[ 0 ] );

			return rootBlock?.name === name;
		},
		[ clientId, name ]
	);

	return isNested ? (
		<div { ...blockProps }>
			<Warning>
				{ __( 'A form cannot be nested within another form.', 'omniform' ) }
			</Warning>
		</div>
	) : ( <div { ...innerBlockProps } /> );
}
