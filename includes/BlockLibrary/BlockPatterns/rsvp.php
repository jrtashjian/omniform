<?php
/**
 * The "RSVP" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'RSVP', 'omniform' ),
	'content' => '
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
		<h2 class="has-text-align-center">Are You Attending?</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">RSVP</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:group -->

		<!-- wp:omniform/field-input {"fieldPlaceholder":"","fieldLabel":"Name","fieldName":"name","isRequired":false} /-->

		<!-- wp:omniform/field-input {"fieldType":"email","fieldPlceholder":"","fieldLabel":"E-Mail","fieldName":"e-mail","isRequired":false} /-->

		<!-- wp:omniform/fieldset {"fieldLabel":"Attending?","fieldName":"attending","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0"}}} -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"Joyfully accepts","fieldName":"joyfully-accepts"} /-->

		<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"Regretfully declines","fieldName":"regretfully-declines"} /--></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field-input {"fieldType":"number","fieldPlaceholder":"","fieldLabel":"Number of persons","fieldName":"number-of-persons","isRequired":false} /-->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Confirm"} /--></div>
		<!-- /wp:group -->
	',
);
