<?php
/**
 * The "Feedback" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Feedback', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group"><!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Success! Your submission has been completed.', 'omniform' ) . '","className":"is-style-success"} /-->

		<!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) . '","className":"is-style-error"} /-->

		<!-- wp:paragraph -->
		<p>' . esc_html__( 'We value your feedback! Please take a moment to fill out our website feedback form to let us know how we can improve your experience. Your input is important to us and we appreciate your time. Thank you for visiting our website.', 'omniform' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Name', 'omniform' ) . '","fieldName":"' . esc_attr__( 'name', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Email', 'omniform' ) . '","fieldName":"' . esc_attr__( 'email', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Is this your first time visiting our site?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'is-this-your-first-time-visiting-our-site', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'No', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Yes', 'omniform' ) . '"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Did you find what you were looking for?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'did-you-find-what-you-were-looking-for', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'No', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Yes', 'omniform' ) . '"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/fieldset {"fieldLabel":"' . esc_html__( 'Please rate our website', 'omniform' ) . '","fieldName":"' . esc_attr__( 'please-rate-our-website', 'omniform' ) . '","style":{"spacing":{"blockGap":"0.75em"}}} -->
		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( '1 - Very Bad', 'omniform' ) . '","fieldName":"' . esc_attr__( '1-very-bad', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( '2 - Poor', 'omniform' ) . '","fieldName":"' . esc_attr__( '2-poor', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( '3 - Average', 'omniform' ) . '","fieldName":"' . esc_attr__( '3-average', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( '4 - Good', 'omniform' ) . '","fieldName":"' . esc_attr__( '4-good', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( '5 - Excellent', 'omniform' ) . '","fieldName":"' . esc_attr__( '5-excellent', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'How could we improve?', 'omniform' ) . '","fieldName":"' . esc_attr__( 'how-could-we-improve', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/textarea /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Send Feedback', 'omniform' ) . '"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
