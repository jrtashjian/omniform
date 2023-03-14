<?php
/**
 * The "Feedback" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Feedback', 'omniform' ),
	'content' => '
		<!-- wp:heading -->
		<h2>Feedback</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>We value your feedback! Please take a moment to fill out our website feedback form to let us know how we can improve your experience. Your input is important to us and we appreciate your time. Thank you for visiting our website.</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field-input {"fieldLabel":"Name","fieldName":"name"} /-->

		<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Email","fieldName":"email"} /-->

		<!-- wp:omniform/field-select {"fieldLabel":"Is this your first time visiting our site?","fieldName":"is-this-your-first-time-visiting-our-site"} -->
		<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
		<!-- /wp:omniform/field-select -->

		<!-- wp:omniform/field-select {"fieldLabel":"Did you find what you were looking for?","fieldName":"did-you-find-what-you-were-looking-for"} -->
		<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
		<!-- /wp:omniform/field-select -->

		<!-- wp:omniform/fieldset {"fieldLabel":"Please rate our website","fieldName":"please-rate-our-website","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"1 - Very Bad","fieldName":"1-very-bad"} /-->

		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"2 - Poor","fieldName":"2-poor"} /-->

		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"3 - Average","fieldName":"3-average"} /-->

		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"4 - Good","fieldName":"4-good","isRequired":false} /-->

		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"5 - Excellent","fieldName":"5-excellent","isRequired":false} /-->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field-textarea {"fieldLabel":"How could we improve?","fieldName":"how-could-we-improve"} /-->

		<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Feedback"} /-->
	',
);
