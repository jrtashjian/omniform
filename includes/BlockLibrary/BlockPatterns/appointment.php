<?php
/**
 * The "Appointment" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Appointment', 'omniform' ),
	'content' => '
		<!-- wp:heading {"className":"wp-block-heading"} -->
		<h2 class="wp-block-heading">Book an Appointment</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Please fill out the form below to make an appointment.</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/fieldset {"fieldLabel":"Your Name","fieldName":"your-name","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field-input {"fieldLabel":"First Name","fieldName":"first-name"} /-->

		<!-- wp:omniform/field-input {"fieldLabel":"Last Name","fieldName":"last-name"} /--></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field-input {"fieldType":"tel","fieldLabel":"Phone number","fieldName":"phone-number"} /-->

		<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Email","fieldName":"email"} /-->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field-input {"fieldType":"date","fieldLabel":"Preferred Date","fieldName":"preferred-date"} /-->

		<!-- wp:omniform/field-select {"fieldLabel":"Preferred Time","fieldName":"preferred-time"} -->
		<!-- wp:omniform/select-option {"fieldLabel":"Morning"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"Afternoon"} /-->
		<!-- /wp:omniform/field-select --></div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Leave a Request"} /--></div>
		<!-- /wp:group -->
	',
);
