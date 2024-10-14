/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	__experimentalNavigatorProvider as NavigatorProvider,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalNavigatorButton as NavigatorButton,
	__experimentalNavigatorBackButton as NavigatorBackButton,
	Button,
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
	Icon,
} from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import {
	store as blockEditorStore,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './index.scss';
import { iconHCaptcha, iconReCaptcha, iconTurnstile } from '../../../shared/icons';

export default function QuickStartPlaceholder( clientId ) {
	// define a state to store each slection from each step.
	const [ goal, setGoal ] = useState( null );
	const [ trackPerformance, setTrackPerformance ] = useState( null );
	const [ spamProtection, setSpamProtection ] = useState( null );

	const { replaceBlock } = useDispatch( blockEditorStore );

	const createForm = () => {
		const block = createBlock( 'omniform/form', {}, [
			createBlock( 'core/group', {}, [
				createBlock( 'omniform/field', {
					fieldLabel: 'Your Name',
				}, [
					createBlock( 'omniform/label', {}, [] ),
					createBlock( 'omniform/input', {}, [] ),
				] ),
				createBlock( 'omniform/field', {
					fieldLabel: 'Your Email Address',
				}, [
					createBlock( 'omniform/label', {}, [] ),
					createBlock( 'omniform/input', {}, [] ),
				] ),
				createBlock( 'omniform/button', {
					buttonLabel: 'Submit',
				}, [] ),
			] ),
		] );

		block.clientId = clientId.clientId;
		replaceBlock( clientId, block );
	};

	return (
		<NavigatorProvider initialPath="/" className="omniform-quick-start">
			<QuickStartScreen
				path="/"
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
					onClick={ () => createForm() }
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
					onClick={ () => setSpamProtection( 'cloudflare-turnstile' ) }
					isPressed={ spamProtection === 'cloudflare-turnstile' }
					icon={ iconTurnstile }
					title={ __( 'Cloudflare Turnstile', 'omniform' ) }
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/google-recaptcha"
					onClick={ () => setSpamProtection( 'google-recaptcha' ) }
					isPressed={ spamProtection === 'google-recaptcha' }
					icon={ iconReCaptcha }
					title={ __( 'Google reCAPTCHA', 'omniform' ) }
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/hcaptcha"
				title={ __( 'hCaptcha', 'omniform' ) }
				subtitle={ __( 'Add hCaptcha to your form.', 'omniform' ) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			/>

			<QuickStartScreen
				path="/cloudflare-turnstile"
				title={ __( 'Cloudflare Turnstile', 'omniform' ) }
				subtitle={ __( 'Add Cloudflare Turnstile to your form.', 'omniform' ) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			/>

			<QuickStartScreen
				path="/google-recaptcha"
				title={ __( 'Google reCAPTCHA', 'omniform' ) }
				subtitle={ __( 'Add Google reCAPTCHA to your form.', 'omniform' ) }
				showBackButton
				finishLabel={ __( 'Finish and Add Form', 'omniform' ) }
				finishCallback={ () => createForm() }
			/>
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
	finishCallback = () => {},
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
					onClick={ finishCallback }
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
	onClick = () => {},
	icon,
	title,
	description,
	isPressed,
} ) => (
	<Component
		className="omniform-quick-start__option"
		path={ path }
		onClick={ onClick }
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

// border: 2px solid #e0e0e0;
// border-radius: 8px;
