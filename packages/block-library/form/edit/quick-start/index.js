/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	__experimentalNavigatorProvider as NavigatorProvider,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalNavigatorButton as NavigatorButton,
	__experimentalNavigatorBackButton as NavigatorBackButton,
	Button,
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
	Icon,
	SVG,
	Path,
	TextControl,
	ExternalLink,
} from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import { useDispatch } from '@wordpress/data';
import {
	useEntityProp,
	store as coreDataStore,
} from '@wordpress/core-data';
import { useState } from '@wordpress/element';
import {
	store as blockEditorStore,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './index.scss';
import { iconHCaptcha, iconReCaptcha, iconTurnstile } from '../../../shared/icons';

/**
 * Generates a form block based on the selected goal, tracking preference, and spam protection.
 *
 * @param {string}  goal              The goal of the form.
 * @param {boolean} isTrackingEnabled Whether tracking is enabled.
 * @param {string}  captchaType       The type of captcha to use.
 *
 * @return {Object} The generated form block.
 */
function generateForm( goal, isTrackingEnabled, captchaType ) {
	let innerBlocks = [];

	switch ( goal ) {
		case 'collect-info':
			innerBlocks = [
				createBlock( 'omniform/field', { fieldLabel: __( 'Your Name', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input' ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Your Email Address', 'omniform' ), isRequired: true }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input', { fieldType: 'email' } ),
				] ),
				createBlock( 'omniform/button', { buttonType: 'submit', buttonLabel: __( 'Submit', 'omniform' ) } ),
			];
			break;
		case 'generate-leads':
			innerBlocks = [
				createBlock( 'omniform/field', { fieldLabel: __( 'Full Name', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input' ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Business Email', 'omniform' ), isRequired: true }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input', { fieldType: 'email' } ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Company Name', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input' ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Phone Number', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input', { fieldType: 'tel' } ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'What are you interested in?', 'omniform' ), isRequired: true }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/textarea' ),
				] ),
				createBlock( 'omniform/button', { buttonType: 'submit', buttonLabel: __( 'Get in Touch', 'omniform' ) } ),
			];
			break;
		case 'get-feedback':
			innerBlocks = [
				createBlock( 'omniform/field', { fieldLabel: __( 'Your Name (Optional)', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input' ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Your Email (Optional)', 'omniform' ) }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/input' ),
				] ),
				createBlock( 'omniform/field', { fieldLabel: __( 'Your Feedback or Suggestions', 'omniform' ), isRequired: true }, [
					createBlock( 'omniform/label' ),
					createBlock( 'omniform/textarea' ),
				] ),
				createBlock( 'omniform/fieldset', { fieldLabel: __( 'Please rate our website', 'omniform' ), isRequired: true }, [
					createBlock( 'omniform/field', { fieldLabel: __( '1 - Very Bad', 'omniform' ), className: 'is-style-inline' }, [
						createBlock( 'omniform/input', { fieldType: 'radio' } ),
						createBlock( 'omniform/label' ),
					] ),
					createBlock( 'omniform/field', { fieldLabel: __( '2 - Poor', 'omniform' ), className: 'is-style-inline' }, [
						createBlock( 'omniform/input', { fieldType: 'radio' } ),
						createBlock( 'omniform/label' ),
					] ),
					createBlock( 'omniform/field', { fieldLabel: __( '3 - Average', 'omniform' ), className: 'is-style-inline' }, [
						createBlock( 'omniform/input', { fieldType: 'radio' } ),
						createBlock( 'omniform/label' ),
					] ),
					createBlock( 'omniform/field', { fieldLabel: __( '4 - Good', 'omniform' ), className: 'is-style-inline' }, [
						createBlock( 'omniform/input', { fieldType: 'radio' } ),
						createBlock( 'omniform/label' ),
					] ),
					createBlock( 'omniform/field', { fieldLabel: __( '5 - Excellent', 'omniform' ), className: 'is-style-inline' }, [
						createBlock( 'omniform/input', { fieldType: 'radio' } ),
						createBlock( 'omniform/label' ),
					] ),
				] ),
				createBlock( 'omniform/button', { buttonType: 'submit', buttonLabel: __( 'Send Feedback', 'omniform' ) } ),
			];
			break;
		default:
	}

	if ( captchaType ) {
		innerBlocks.push(
			createBlock( 'omniform/captcha', { service: captchaType } )
		);
	}

	return isTrackingEnabled
		? createBlock( 'core/group', { layout: { type: 'default' } }, innerBlocks )
		: createBlock( 'omniform/form', {}, [ createBlock( 'core/group', { layout: { type: 'default' } }, innerBlocks ) ] );
}

export default function QuickStartPlaceholder( { clientId, onFinish } ) {
	// define a state to store each slection from each step.
	const [ goal, setGoal ] = useState( null );
	const [ trackPerformance, setTrackPerformance ] = useState( null );
	const [ spamProtection, setSpamProtection ] = useState( null );

	const { replaceBlock } = useDispatch( blockEditorStore );

	const { saveEditedEntityRecord } = useDispatch( coreDataStore );

	const createForm = async () => {
		await saveEditedEntityRecord( 'root', 'site' );

		const block = generateForm( goal, trackPerformance, spamProtection );
		block.clientId = clientId;

		replaceBlock( clientId, block );
		onFinish();
	};

	const SetupInstructions = ( { serviceLabel, setupLink } ) => {
		return (
			<>
				{ sprintf(
					/* translators: 1: captcha service name */
					__( 'To start using %s, you need to sign up for an API key pair for your site. The key pair consists of a site key and secret key.', 'omniform' ),
					serviceLabel
				) }
				&nbsp;
				<ExternalLink href={ setupLink }>
					{ __( 'Generate keys', 'omniform' ) }
				</ExternalLink>
			</>
		);
	};

	return (
		<NavigatorProvider initialPath="/" className="omniform-quick-start">
			<QuickStartScreen
				path="/"
				title={ (
					<SVG viewBox="0 0 216 24" height="24">
						<Path fill="#000" fillRule="evenodd" clipRule="evenodd" d="M16.02 0c2.909 0 5.183.693 6.822 2.078C24.482 3.44 25.3 5.414 25.3 8c0 1.224-.185 2.54-.554 3.948-.693 2.586-1.767 4.78-3.221 6.58-1.455 1.801-3.186 3.163-5.195 4.087-2.008.923-4.19 1.385-6.545 1.385-2.886 0-5.149-.681-6.788-2.043C1.358 20.57.538 18.586.538 16c0-1.178.197-2.528.59-4.052.67-2.563 1.72-4.733 3.151-6.51 1.455-1.802 3.175-3.152 5.16-4.053C11.448.462 13.641 0 16.02 0Zm-.624 3.394c-.693 0-1.316.277-1.87.831-.554.554-1.108 1.466-1.662 2.736-.531 1.247-1.109 2.99-1.732 5.23-.924 3.232-1.385 5.46-1.385 6.683 0 .716.127 1.236.38 1.559.255.323.659.485 1.213.485.692 0 1.316-.277 1.87-.831.554-.578 1.097-1.49 1.628-2.736.53-1.247 1.12-2.967 1.766-5.16.9-3.187 1.35-5.426 1.35-6.72 0-.738-.127-1.269-.38-1.592-.254-.323-.647-.485-1.178-.485ZM148.058 2.078C146.419.693 144.144 0 141.235 0c-2.378 0-4.571.462-6.58 1.385-1.985.9-3.705 2.251-5.16 4.052-1.431 1.778-2.482 3.948-3.151 6.511-.393 1.524-.589 2.874-.589 4.052 0 2.586.819 4.571 2.459 5.957 1.639 1.362 3.901 2.043 6.787 2.043 2.355 0 4.537-.462 6.546-1.385 2.009-.924 3.74-2.286 5.195-4.087 1.454-1.8 2.528-3.994 3.221-6.58.369-1.408.554-2.724.554-3.948 0-2.586-.82-4.56-2.459-5.922Zm-9.316 2.147c.554-.554 1.177-.831 1.87-.831.531 0 .923.162 1.177.485.254.323.381.854.381 1.593 0 1.293-.45 3.532-1.35 6.718-.647 2.194-1.236 3.914-1.767 5.16-.531 1.247-1.073 2.16-1.627 2.736-.554.555-1.178.832-1.87.832-.554 0-.959-.162-1.212-.485-.254-.323-.381-.843-.381-1.559 0-1.223.461-3.451 1.385-6.683.623-2.24 1.2-3.983 1.731-5.23.555-1.27 1.109-2.182 1.663-2.736Z" />
						<Path fill="#000" d="M126.47 1.022c-.461-.37-1.119-.555-1.974-.555H109.12c-.624 0-1.097.07-1.42.208-.323.116-.531.324-.624.624a1.916 1.916 0 0 0-.069.38c0 .185.104.428.312.728.162.208.277.404.346.589.093.184.139.415.139.692 0 .254-.046.531-.139.831l-4.26 14.927c-.138.53-.323.923-.554 1.177a3.976 3.976 0 0 1-.935.728c-.254.161-.438.311-.554.45a.848.848 0 0 0-.277.485c-.092.392.012.704.312.935.3.207.75.311 1.35.311h8.693c.716 0 1.258-.08 1.628-.242.369-.162.554-.45.554-.866a.696.696 0 0 0-.139-.45c-.092-.139-.265-.312-.519-.52-.3-.23-.531-.45-.693-.657-.138-.208-.208-.474-.208-.797 0-.231.024-.416.07-.554l1.454-5.16h1.005c.277 0 .484.046.623.138.139.093.289.3.45.624l.624 1.177c.415.762.992 1.143 1.731 1.143.901 0 1.524-.566 1.87-1.697l1.524-5.264a2.57 2.57 0 0 0 .104-.658c0-.462-.15-.82-.45-1.074-.277-.254-.635-.38-1.074-.38-.346 0-.681.08-1.004.242-.3.138-.647.38-1.039.727l-1.074.9c-.277.254-.542.428-.796.52a2.236 2.236 0 0 1-.831.139h-.658l1.939-6.858h3.463c.439 0 .727.058.866.173.139.116.254.347.346.693l.554 1.974c.162.554.416.958.762 1.212.347.231.808.347 1.386.347.6 0 1.085-.127 1.454-.381.37-.254.635-.681.797-1.282l.9-3.22c.092-.3.139-.612.139-.936 0-.646-.243-1.154-.728-1.523Z" />
						<Path fill="#000" fillRule="evenodd" clipRule="evenodd" d="M176.396 7.342c-.369 1.339-1.062 2.39-2.078 3.151-1.016.74-2.297 1.201-3.844 1.386 1.108.092 1.939.415 2.494.97.554.553.831 1.28.831 2.181 0 .347-.058.774-.173 1.282l-.381 1.697c-.047.23-.07.392-.07.484 0 .3.07.531.208.693.139.139.335.208.589.208.485 0 .831.07 1.039.208.231.138.312.392.242.761-.092.532-.369 1.051-.831 1.559-.462.508-1.12.935-1.974 1.281-.831.324-1.847.485-3.048.485-1.523 0-2.735-.335-3.636-1.004-.9-.693-1.351-1.674-1.351-2.944 0-.485.07-1.004.208-1.558l.554-2.217c.093-.3.139-.612.139-.935 0-.53-.185-.912-.554-1.143-.347-.23-.912-.346-1.697-.346h-1.316l-1.593 5.749c-.046.139-.07.312-.07.52 0 .323.081.6.243.83.161.232.381.474.658.728.208.162.369.323.485.485a.692.692 0 0 1 .173.45c0 .392-.185.67-.554.831-.37.162-.912.242-1.628.242h-8.693c-.6 0-1.05-.103-1.35-.311-.3-.231-.404-.543-.312-.935a.849.849 0 0 1 .277-.485c.116-.139.3-.289.554-.45.393-.231.704-.474.935-.728.231-.254.416-.646.554-1.177l4.226-14.926c.092-.3.138-.59.138-.866 0-.254-.046-.462-.138-.624a4.019 4.019 0 0 0-.312-.623c-.208-.3-.312-.543-.312-.727 0-.07.023-.197.069-.381a.936.936 0 0 1 .589-.624c.323-.138.808-.207 1.455-.207h11.601c1.594 0 2.979.242 4.156.727 1.201.485 2.113 1.154 2.736 2.009.647.83.97 1.777.97 2.84 0 .484-.069.97-.208 1.454Zm-8.866-.346c.116-.531.174-.935.174-1.213 0-1.223-.67-1.835-2.009-1.835h-1.455l-1.523 6.13h1.454c.9 0 1.605-.22 2.113-.658.531-.439.946-1.247 1.246-2.424Z" />
						<Path fill="#000" d="M215.045.71c-.253-.162-.623-.243-1.108-.243h-5.16c-.947 0-1.708.127-2.286.381-.577.231-1.166.716-1.766 1.455l-9.385 11.671-3.463-11.498c-.208-.739-.485-1.258-.831-1.558-.324-.3-.832-.45-1.524-.45h-5.368c-.554 0-.97.092-1.247.277-.254.184-.381.484-.381.9 0 .323.058.658.173 1.004l.554 1.836-4.225 14.96c-.161.555-.358.959-.589 1.213a5.06 5.06 0 0 1-.9.693 5.824 5.824 0 0 0-.381.242 1.158 1.158 0 0 0-.45.589 1.155 1.155 0 0 0-.104.45c0 .277.127.496.381.658.277.162.658.242 1.143.242h2.874c.647 0 1.132-.057 1.455-.173.323-.138.531-.37.623-.692.046-.185.069-.3.069-.347 0-.184-.104-.427-.311-.727a3.542 3.542 0 0 1-.381-.589 1.997 1.997 0 0 1-.104-.692c0-.277.046-.566.138-.866l2.563-8.97 3.256 10.701c.254.855.531 1.466.831 1.836.323.346.762.52 1.316.52.577 0 1.085-.162 1.524-.486.461-.346 1.062-.97 1.8-1.87l8.97-11.116-2.632 9.35c-.162.531-.358.924-.589 1.178a3.574 3.574 0 0 1-.9.692 1.322 1.322 0 0 1-.243.174 1.5 1.5 0 0 0-.346.277 1.314 1.314 0 0 0-.277.415 1.155 1.155 0 0 0-.104.45c0 .277.127.497.381.658.277.162.658.243 1.143.243h8.139c.623 0 1.096-.07 1.419-.208.347-.139.566-.358.658-.658.047-.185.07-.3.07-.346a.729.729 0 0 0-.104-.381 2.094 2.094 0 0 0-.243-.381c-.138-.231-.254-.427-.346-.589a1.88 1.88 0 0 1-.104-.658c0-.277.046-.566.139-.866l4.225-14.892c.161-.53.346-.923.554-1.177.231-.254.531-.485.9-.693a6.75 6.75 0 0 0 .554-.38c.139-.116.243-.278.312-.485.069-.185.104-.335.104-.45 0-.278-.138-.486-.416-.624ZM62.3.71c-.255-.162-.624-.243-1.109-.243h-5.16c-.947 0-1.709.127-2.286.381-.577.231-1.166.716-1.766 1.455l-9.385 11.671L39.13 2.476c-.207-.739-.485-1.258-.83-1.558-.324-.3-.832-.45-1.525-.45h-5.368c-.554 0-.97.092-1.246.277-.254.184-.381.484-.381.9 0 .323.057.658.173 1.004l.554 1.836-4.225 14.96c-.162.555-.358.959-.589 1.213-.23.23-.53.462-.9.693a5.748 5.748 0 0 0-.381.242 1.161 1.161 0 0 0-.45.589 1.15 1.15 0 0 0-.104.45c0 .277.127.496.38.658.278.162.659.242 1.143.242h2.875c.646 0 1.131-.057 1.454-.173.324-.138.531-.37.624-.692.046-.185.07-.3.07-.347 0-.184-.105-.427-.313-.727a3.586 3.586 0 0 1-.38-.589 1.99 1.99 0 0 1-.104-.692c0-.277.046-.566.138-.866l2.563-8.97 3.255 10.701c.254.855.531 1.466.831 1.836.324.346.762.52 1.317.52a2.5 2.5 0 0 0 1.523-.486c.462-.346 1.062-.97 1.801-1.87l8.97-11.116-2.632 9.35c-.162.531-.358.924-.589 1.178-.23.254-.531.485-.9.692a1.335 1.335 0 0 1-.243.174 1.484 1.484 0 0 0-.346.277 1.312 1.312 0 0 0-.277.415 1.15 1.15 0 0 0-.104.45c0 .277.127.497.38.658.278.162.659.243 1.144.243h8.138c.624 0 1.097-.07 1.42-.208.346-.139.566-.358.658-.658.046-.185.07-.3.07-.346a.727.727 0 0 0-.104-.381 2.141 2.141 0 0 0-.243-.381c-.138-.231-.254-.427-.346-.589a1.876 1.876 0 0 1-.104-.658c0-.277.046-.566.138-.866L60.291 4.52c.161-.53.346-.923.554-1.177.23-.254.53-.485.9-.693.254-.161.439-.288.554-.38.139-.116.243-.278.312-.485.07-.185.104-.335.104-.45 0-.278-.139-.486-.416-.624ZM87.604.398c.946 0 1.42.3 1.42.9 0 .162-.012.278-.035.347-.07.23-.173.416-.312.554a5.914 5.914 0 0 1-.589.416 3.801 3.801 0 0 0-.865.692c-.231.254-.427.647-.59 1.178L82.34 19.86c-.393 1.386-.89 2.355-1.49 2.91-.577.554-1.385.83-2.424.83-.923 0-1.697-.265-2.32-.796-.6-.554-1.166-1.397-1.697-2.528l-5.23-11.013-2.909 10.113c-.069.3-.104.565-.104.796 0 .3.047.554.139.762.115.185.265.381.45.589.254.3.381.554.381.762 0 .138-.011.242-.034.311-.093.324-.3.554-.624.693-.323.115-.808.173-1.454.173h-3.775c-.947 0-1.42-.3-1.42-.9 0-.162.011-.277.034-.347.07-.23.173-.404.312-.52.139-.138.335-.288.589-.45.346-.207.635-.438.866-.692.23-.254.427-.646.588-1.177l4.399-15.55-.555-1.143c-.23-.462-.346-.866-.346-1.212 0-.716.462-1.074 1.386-1.074h5.125c.739 0 1.293.173 1.662.52.393.323.808.912 1.247 1.766l5.056 10.147 2.39-8.346c.07-.323.104-.589.104-.797 0-.3-.058-.542-.173-.727a2.442 2.442 0 0 0-.416-.623c-.254-.3-.38-.554-.38-.762 0-.139.01-.243.034-.312a.94.94 0 0 1 .623-.658c.323-.138.808-.208 1.455-.208h3.775ZM103.523.71c-.254-.162-.623-.243-1.108-.243h-8.139c-.646 0-1.131.07-1.454.208a.934.934 0 0 0-.589.624c-.046.184-.07.311-.07.38 0 .185.105.428.312.728.139.23.243.439.312.623.092.162.139.37.139.624 0 .277-.047.565-.139.865l-4.225 14.927c-.162.554-.358.958-.589 1.212-.23.23-.53.462-.9.693a5.709 5.709 0 0 0-.381.242 1.161 1.161 0 0 0-.45.589 1.15 1.15 0 0 0-.104.45c0 .277.138.496.415.658.254.162.624.242 1.109.242H95.8c.647 0 1.131-.057 1.455-.173.323-.138.53-.37.623-.692.046-.185.07-.3.07-.347 0-.184-.105-.427-.312-.727a3.586 3.586 0 0 1-.381-.589 1.99 1.99 0 0 1-.104-.692c0-.277.046-.566.138-.866l4.191-14.927c.161-.53.358-.923.589-1.177.23-.254.531-.485.9-.693a.793.793 0 0 1 .208-.138 1.82 1.82 0 0 0 .346-.277c.139-.139.242-.289.312-.45.069-.185.104-.335.104-.45 0-.278-.139-.486-.416-.624Z" />
					</SVG>
				) }
				subtitle={ __( 'Start your form creation process.', 'omniform' ) }
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/define-your-goal"
					title={ __( 'Quick Start', 'omniform' ) }
					description={ __( 'Let us guide you through building your form', 'omniform' ) }
				/>

				<QuickStartOption
					as={ Button }
					onClick={ () => onFinish() }
					title={ __( 'Blank Form', 'omniform' ) }
					description={ __( 'Build you form from scratch', 'omniform' ) }
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/define-your-goal"
				title={ __( 'Define Your Goal', 'omniform' ) }
				subtitle={ __( 'What do you want to achieve with this form?', 'omniform' ) }
				showBackButton
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'collect-info' ) }
					isPressed={ goal === 'collect-info' }
					title={ __( 'Collect Info', 'omniform' ) }
					description={ __( 'Gather basic details like names and emails', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'generate-leads' ) }
					isPressed={ goal === 'generate-leads' }
					title={ __( 'Generate Leads', 'omniform' ) }
					description={ __( 'Capture potential customers for follow-up', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'get-feedback' ) }
					isPressed={ goal === 'get-feedback' }
					title={ __( 'Get Feedback', 'omniform' ) }
					description={ __( 'Receive opinions or suggestions from visitors', 'omniform' ) }
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/track-performance"
				title={ __( 'Track Performance', 'omniform' ) }
				subtitle={ __( 'Do you want to monitor traffic and conversions?', 'omniform' ) }
				showBackButton
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/spam-protection"
					onClick={ () => setTrackPerformance( true ) }
					isPressed={ trackPerformance === true }
					title={ __( 'Track visits and conversion rates', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/spam-protection"
					onClick={ () => setTrackPerformance( false ) }
					isPressed={ trackPerformance === false }
					title={ __( 'No tracking needed', 'omniform' ) }
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/spam-protection"
				title={ __( 'Spam Protection', 'omniform' ) }
				subtitle={ __( 'Add spam protection to your form.', 'omniform' ) }
				showBackButton
				finishLabel={ __( 'Add Form Without Protection', 'omniform' ) }
				finishCallback={ () => createForm() }
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/hcaptcha"
					onClick={ () => setSpamProtection( 'hcaptcha' ) }
					isPressed={ spamProtection === 'hcaptcha' }
					icon={ iconHCaptcha }
					title={ __( 'hCaptcha', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/cloudflare-turnstile"
					onClick={ () => setSpamProtection( 'turnstile' ) }
					isPressed={ spamProtection === 'turnstile' }
					icon={ iconTurnstile }
					title={ __( 'Cloudflare Turnstile', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/google-recaptcha"
					onClick={ () => setSpamProtection( 'recaptchav2' ) }
					isPressed={ spamProtection === 'recaptchav2' }
					icon={ iconReCaptcha }
					title={ __( 'Google reCAPTCHA', 'omniform' ) }
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/hcaptcha"
				title={ __( 'hCaptcha', 'omniform' ) }
				subtitle={ (
					<SetupInstructions
						serviceLabel={ __( 'hCaptcha', 'omniform' ) }
						setupLink="https://dashboard.hcaptcha.com/sites/new"
					/>
				) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			>
				<CaptchaSettingControls service="hcaptcha" />
			</QuickStartScreen>

			<QuickStartScreen
				path="/cloudflare-turnstile"
				title={ __( 'Cloudflare Turnstile', 'omniform' ) }
				subtitle={ (
					<SetupInstructions
						serviceLabel={ __( 'Turnstile', 'omniform' ) }
						setupLink="https://dash.cloudflare.com/?to=:/account/turnstile"
					/>
				) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			>
				<CaptchaSettingControls service="turnstile" />
			</QuickStartScreen>

			<QuickStartScreen
				path="/google-recaptcha"
				title={ __( 'Google reCAPTCHA', 'omniform' ) }
				subtitle={ (
					<SetupInstructions
						serviceLabel={ __( 'reCAPTCHA v2', 'omniform' ) }
						setupLink="https://www.google.com/recaptcha/admin/create"
					/>
				) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			>
				<CaptchaSettingControls service="recaptchav2" />
			</QuickStartScreen>
		</NavigatorProvider>
	);
}

const QuickStartScreen = ( {
	path,
	title,
	subtitle,
	showBackButton,
	children,
	finishLabel,
	finishCallback,
} ) => (
	<NavigatorScreen path={ path } className="omniform-quick-start__step">
		<div className="omniform-quick-start__title">{ title }</div>
		<div className="omniform-quick-start__subtitle">{ subtitle }</div>

		<HStack alignment="center" justify="center" spacing={ 4 }>
			{ children }
		</HStack>

		<HStack className="omniform-quick-start__buttons">
			{ showBackButton && (
				<NavigatorBackButton className="omniform-quick-start__buttons-back" variant="tertiary">
					{ __( 'Back', 'omniform' ) }
				</NavigatorBackButton>
			) }

			{ finishLabel && (
				<Button
					className="omniform-quick-start__buttons-finish"
					variant="primary"
					onClick={ () => finishCallback?.() }
				>
					{ finishLabel }
				</Button>
			) }
		</HStack>
	</NavigatorScreen>
);

const QuickStartOption = ( {
	as: Component = NavigatorButton,
	path,
	onClick,
	icon,
	title,
	description,
	isPressed,
} ) => (
	<Component
		className="omniform-quick-start__option"
		path={ path }
		onClick={ () => onClick?.() }
		isPressed={ isPressed }
	>
		<VStack alignment="center" justify="center">
			{ icon && (
				<Icon
					className="omniform-quick-start__option-icon"
					icon={ icon }
					size={ 48 }
				/>
			) }
			<div className="omniform-quick-start__option-title">{ title }</div>
			{ description && (
				<div className="omniform-quick-start__option-description">{ description }</div>
			) }
		</VStack>
	</Component>
);

const CaptchaSettingControls = ( { service } ) => {
	const [ siteKey, setSiteKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_site_key` );
	const [ secretKey, setSecretKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_secret_key` );

	return (
		<>
			<TextControl
				label={ __( 'Site Key', 'omniform' ) }
				value={ siteKey || '' }
				onChange={ setSiteKey }
				type="password"
			/>

			<TextControl
				label={ __( 'Secret Key', 'omniform' ) }
				value={ secretKey || '' }
				onChange={ setSecretKey }
				type="password"
			/>
		</>
	);
};
