<?php
/**
 * The "Membership Registration" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Membership Registration', 'omniform' ),
	'content' => '
		<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"5em","bottom":"5em","left":"5em","right":"5em"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group alignfull" style="padding-top:5em;padding-right:5em;padding-bottom:5em;padding-left:5em"><!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Success! Your submission has been completed.', 'omniform' ) . '","style":{"border":{"left":{"color":"var(--wp--preset--color--vivid-green-cyan,#00d084)","width":"6px"}},"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1.5em","right":"1.5em"}}}} /-->

		<!-- wp:omniform/response-notification {"messageType":"error","messageContent":"' . esc_html__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) . '","style":{"border":{"left":{"color":"var(--wp--preset--color--vivid-red,#cf2e2e)","width":"6px"}},"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1.5em","right":"1.5em"}}}} /-->

		<!-- wp:paragraph -->
		<p>' . esc_html__( 'Join our community and take advantage of our member perks! Sign up for a membership and unlock access to special features and discounts.', 'omniform' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'What\'s your name?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'whats-your-name', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'What\'s your email address?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'whats-your-email-address', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'What\'s your phone number?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'whats-your-phone-number', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"tel"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'How did you hear about us?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'how-did-you-hear-about-us', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Referral from a friend or colleague', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Social media', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Online search', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Advertising', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Trade show or event', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Email or newsletter', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Radio or TV', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Print', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Other', 'omniform' ) . '"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Why do you want to be a member?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'why-do-you-want-to-be-a-member', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/textarea {"style":{"dimensions":{"minHeight":"230px"}}} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Send', 'omniform' ) . '"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
