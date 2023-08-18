<?php
/**
 * The "Contact" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Contact', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:heading -->
		<h2 class="wp-block-heading">Contact Us</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>If you have any questions or comments, or if you\'d like to work with me or collaborate on a project, please don\'t hesitate to get in touch. I look forward to hearing from you!</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field {"fieldLabel":"Your email address","fieldName":"your-email-address"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Your message","fieldName":"your-message"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/textarea {"style":{"dimensions":{"minHeight":"230px"}}} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
