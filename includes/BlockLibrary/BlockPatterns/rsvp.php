<?php
/**
 * The "RSVP" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'RSVP', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group"><!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Success! Your submission has been completed.', 'omniform' ) . '","className":"is-style-success"} /-->

		<!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) . '","className":"is-style-error"} /-->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Name', 'omniform' ) . '","fieldName":"' . esc_html__( 'name', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'E-mail', 'omniform' ) . '","fieldName":"' . esc_html__( 'e-mail', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/fieldset {"fieldLabel":"' . esc_html__( 'Attending?', 'omniform' ) . '","fieldName":"' . esc_html__( 'attending', 'omniform' ) . '"} -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75em","bottom":"0.75em"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group" style="padding-top:0.75em;padding-bottom:0.75em"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Joyfully accepts', 'omniform' ) . '","fieldName":"' . esc_html__( 'joyfully-accepts', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Regretfully declines', 'omniform' ) . '","fieldName":"' . esc_html__( 'regretfully-declines', 'omniform' ) . '","className":"is-style-inline"} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Number of persons', 'omniform' ) . '","fieldName":"' . esc_html__( 'number-of-persons', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"number"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Confirm', 'omniform' ) . '"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
