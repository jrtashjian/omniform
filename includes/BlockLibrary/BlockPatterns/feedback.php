<?php
/**
 * The "Feedback" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Feedback', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:heading -->
		<h2 class="wp-block-heading">Feedback</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>We value your feedback! Please take a moment to fill out our website feedback form to let us know how we can improve your experience. Your input is important to us and we appreciate your time. Thank you for visiting our website.</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field {"fieldLabel":"Name","fieldName":"name"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Email","fieldName":"email"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Is this your first time visiting our site?","fieldName":"is-this-your-first-time-visiting-our-site"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Did you find what you were looking for?","fieldName":"did-you-find-what-you-were-looking-for"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/fieldset {"fieldLabel":"Please rate our website","fieldName":"please-rate-our-website","style":{"spacing":{"blockGap":"0.75em"}}} -->
		<!-- wp:omniform/field {"fieldLabel":"1 - Very Bad","fieldName":"1-very-bad","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"2 - Poor","fieldName":"2-poor","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"3 - Average","fieldName":"3-average","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"4 - Good","fieldName":"4-good","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"5 - Excellent","fieldName":"5-excellent","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"How could we improve?","fieldName":"how-could-we-improve"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/textarea {"style":{"dimensions":{"minHeight":"230px"}}} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Feedback"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
