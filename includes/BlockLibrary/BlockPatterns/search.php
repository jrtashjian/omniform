<?php
/**
 * The "RSVP" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Search', 'omniform' ),
	'content' => '
		<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","orientation":"horizontal"}} -->
		<div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Search', 'omniform' ) . '","fieldName":"s","isRequired":true,"style":{"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"space-between"}} -->
		<!-- wp:omniform/input {"fieldType":"search","fieldPlaceholder":"","style":{"layout":{"selfStretch":"fill","flexSize":null}}} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Search","style":{"layout":{"selfStretch":"fit","flexSize":null}}} /--></div>
		<!-- /wp:group -->
	',
	'type'    => 'custom',
	'meta'    => array(
		'submit_method' => 'GET',
		'submit_action' => '{{get_site_url}}',
	),
);
