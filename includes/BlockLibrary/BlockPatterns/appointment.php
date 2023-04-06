<?php
/**
 * The "Appointment" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Appointment', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:heading {"className":"wp-block-heading"} -->
		<h2 class="wp-block-heading">Book an Appointment</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Please fill out the form below to make an appointment.</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/fieldset {"fieldLabel":"Your Name","fieldName":"your-name"} -->
		<!-- wp:group {"style":{"spacing":{}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field {"fieldLabel":"First Name","fieldName":"first-name","style":{"layout":{"selfStretch":"fill","flexSize":null}}} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Last Name","fieldName":"last-name","style":{"layout":{"selfStretch":"fill","flexSize":null}}} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"Phone Number","fieldName":"phone-number"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"tel"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Email","fieldName":"email"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field {"fieldLabel":"Preferred Date","fieldName":"preferred-date","style":{"layout":{"selfStretch":"fill","flexSize":null}}} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"date"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"Preferred Time","fieldName":"preferred-time","style":{"layout":{"selfStretch":"fill","flexSize":null}}} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"Morning"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Afternoon"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Leave a Request"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
