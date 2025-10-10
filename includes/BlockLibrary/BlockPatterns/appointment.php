<?php
/**
 * The "Appointment" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Book an Appointment', 'omniform' ),
	'content' => '
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group"><!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Success! Your submission has been completed.', 'omniform' ) . '","className":"is-style-success"} /-->

		<!-- wp:omniform/response-notification {"messageContent":"' . esc_html__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) . '","className":"is-style-error"} /-->

		<!-- wp:paragraph -->
		<p>' . esc_html__( 'Please fill out the form below to make an appointment.', 'omniform' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:omniform/fieldset {"fieldLabel":"' . esc_html__( 'Your Name', 'omniform' ) . '"} -->
		<!-- wp:group {"style":{"spacing":{}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'First Name', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Last Name', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input /-->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->
		<!-- /wp:omniform/fieldset -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Phone Number', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"tel"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Email', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"email"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Preferred Date', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/input {"fieldType":"date"} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Preferred Time', 'omniform' ) . '"} -->
		<!-- wp:omniform/label /-->

		<!-- wp:omniform/select -->
		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Morning', 'omniform' ) . '"} /-->

		<!-- wp:omniform/select-option {"fieldLabel":"' . esc_html__( 'Afternoon', 'omniform' ) . '"} /-->
		<!-- /wp:omniform/select -->
		<!-- /wp:omniform/field --></div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Leave a Request', 'omniform' ) . '"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
