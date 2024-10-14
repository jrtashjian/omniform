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
				title="Let's Begin"
				subtitle="Start your form creation process."
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/define-your-goal"
					title="Quick Start"
					description="Let us guide you through building your form"
				/>

				<QuickStartOption
					as={ Button }
					onClick={ () => createForm() }
					title="Blank Form"
					description="Build you form from scratch"
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/define-your-goal"
				title="Define Your Goal"
				subtitle="What do you want to achieve with this form?"
				showBackButton
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'collect-info' ) }
					isPressed={ goal === 'collect-info' }
					title="Collect Info"
					description="Gather basic details like names and emails"
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'generate-leads' ) }
					isPressed={ goal === 'generate-leads' }
					title="Generate Leads"
					description="Capture potential customers for follow-up"
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/track-performance"
					onClick={ () => setGoal( 'get-feedback' ) }
					isPressed={ goal === 'get-feedback' }
					title="Get Feedback"
					description="Receive opinions or suggestions from visitors"
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/track-performance"
				title="Track Performance"
				subtitle="Do you want to monitor traffic and conversions?"
				showBackButton
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/spam-protection"
					onClick={ () => setTrackPerformance( true ) }
					isPressed={ trackPerformance === true }
					title="Track visits and conversion rates"
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/spam-protection"
					onClick={ () => setTrackPerformance( false ) }
					isPressed={ trackPerformance === false }
					title="No tracking needed"
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/spam-protection"
				title="Spam Protection"
				subtitle="Add spam protection to your form."
				showBackButton
				finishLabel="Add Form Without Protection"
				finishCallback={ () => createForm() }
			>
				<QuickStartOption
					as={ NavigatorButton }
					path="/hcaptcha"
					onClick={ () => setSpamProtection( 'hcaptcha' ) }
					isPressed={ spamProtection === 'hcaptcha' }
					icon={ iconHCaptcha }
					title="hCaptcha"
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/cloudflare-turnstile"
					onClick={ () => setSpamProtection( 'cloudflare-turnstile' ) }
					isPressed={ spamProtection === 'cloudflare-turnstile' }
					icon={ iconTurnstile }
					title="Cloudflare Turnstile"
				/>

				<QuickStartOption
					as={ NavigatorButton }
					path="/google-recaptcha"
					onClick={ () => setSpamProtection( 'google-recaptcha' ) }
					isPressed={ spamProtection === 'google-recaptcha' }
					icon={ iconReCaptcha }
					title="Google reCAPTCHA"
				/>
			</QuickStartScreen>

			<QuickStartScreen
				path="/hcaptcha"
				title="hCaptcha"
				subtitle="Add hCaptcha to your form."
				showBackButton
				finishLabel="Finish and Add Form"
				finishCallback={ () => createForm() }
			/>

			<QuickStartScreen
				path="/cloudflare-turnstile"
				title="Cloudflare Turnstile"
				subtitle="Add Cloudflare Turnstile to your form."
				showBackButton
				finishLabel="Finish and Add Form"
				finishCallback={ () => createForm() }
			/>

			<QuickStartScreen
				path="/google-recaptcha"
				title="Google reCAPTCHA"
				subtitle="Add Google reCAPTCHA to your form."
				showBackButton
				finishLabel="Finish and Add Form"
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
