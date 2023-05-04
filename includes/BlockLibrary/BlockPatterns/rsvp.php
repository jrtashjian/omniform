<?php
/**
 * The "RSVP" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'RSVP', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
		<h2 class="wp-block-heading has-text-align-center">Are You Attending?</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">RSVP</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:group -->

		<!-- wp:omniform/field {"fieldLabel":"Name","fieldName":"name"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"E-mail","fieldName":"e-mail"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/fieldset {"fieldLabel":"Attending?","fieldName":"attending"} -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75em","bottom":"0.75em"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group" style="padding-top:0.75em;padding-bottom:0.75em"><!-- wp:omniform/field {"fieldLabel":"Joyfully accepts","fieldName":"joyfully-accepts","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Regretfully declines","fieldName":"regretfully-declines","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
		<!-- wp:omniform/input {"fieldType":"radio"} /-->

		<!-- wp:omniform/label /-->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"Number of persons","fieldName":"number-of-persons"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"number"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Confirm"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
