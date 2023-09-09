<?php
/**
 * The "RSVP" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'RSVP', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
		<h2 class="wp-block-heading has-text-align-center">' . esc_html__( 'Are You Attending?', 'omniform' ) . '</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">' . esc_html__( 'RSVP', 'omniform' ) . '</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:group -->

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
		<div class="wp-block-group" style="padding-top:0.75em;padding-bottom:0.75em"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Joyfully accepts', 'omniform' ) . '","fieldName":"' . esc_html__( 'joyfully-accepts', 'omniform' ) . '","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Regretfully declines', 'omniform' ) . '","fieldName":"' . esc_html__( 'regretfully-declines', 'omniform' ) . '","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
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
