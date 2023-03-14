<?php
/**
 * The "Membership Registration" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Membership Registration', 'omniform' ),
	'content' => '
		<!-- wp:heading -->
		<h2>Membership Registration</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Join our community and take advantage of our member perks! Sign up for a membership and unlock access to special features and discounts.</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field-input {"fieldLabel":"What\'s your name?","fieldName":"whats-your-name"} /-->

		<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"What\'s your email address?","fieldName":"whats-your-email-address"} /-->

		<!-- wp:omniform/field-input {"fieldType":"tel","fieldLabel":"What\'s your phone number?","fieldName":"whats-your-phone-number"} /-->

		<!-- wp:omniform/field-select {"fieldPlaceholder":" ","fieldLabel":"How did you hear about us?","fieldName":"how-did-you-hear-about-us"} -->
		<!-- wp:omniform/select-option {"fieldLabel":"Referral from a friend or colleague"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Social media"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Online search"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Advertising"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Trade show or event"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Email or newsletter"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Radio or TV"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Print"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Other"} /-->
		<!-- /wp:omniform/field-select -->

		<!-- wp:omniform/field-textarea {"fieldLabel":"Why do you want to be a member?","fieldName":"why-do-you-want-to-be-a-member"} /-->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send"} /--></div>
		<!-- /wp:group -->
	',
);
