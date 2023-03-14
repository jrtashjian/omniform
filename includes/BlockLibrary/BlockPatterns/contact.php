<?php
/**
 * The "Contact" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Contact', 'omniform' ),
	'content' => '
		<!-- wp:heading -->
		<h2>' . __( 'Contact Us', 'omniform' ) . '</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>' . __( 'If you have any questions or comments, or if you\'d like to work with me or collaborate on a project, please don\'t hesitate to get in touch. I look forward to hearing from you!', 'omniform' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"' . __( 'Your email address', 'omniform' ) . '","fieldName":"your-email-address","isRequired":false} /-->

		<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","fieldName":"' . __( 'your-message', 'omniform' ) . '","isRequired":false} /-->

		<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . __( 'Send Message', 'omniform' ) . '"} /-->
	',
);
