<?php
/**
 * The "Contact" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Contact', 'omniform' ),
	'content' => '
		<!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
		<div class="wp-block-group alignfull"><!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Success! Your submission has been completed.', 'omniform' ) . '","className":"is-style-success"} /-->

		<!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) . '","className":"is-style-error"} /-->

		<!-- wp:paragraph -->
		<p>' . esc_html__( 'If you have any questions or comments, or if you\'d like to work with me or collaborate on a project, please don\'t hesitate to get in touch. I look forward to hearing from you!', 'omniform' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Your email address', 'omniform' ) . '","fieldName":"' . esc_attr__( 'your-email-address', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Your message', 'omniform' ) . '","fieldName":"' . esc_attr__( 'your-message', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/textarea /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Send Message', 'omniform' ) . '"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
