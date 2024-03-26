<?php
/**
 * The "Newsletter" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => esc_attr__( 'Newsletter', 'omniform' ),
	'content' => '
		<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"5em","bottom":"5em","left":"5em","right":"5em"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group alignfull" style="padding-top:5em;padding-right:5em;padding-bottom:5em;padding-left:5em"><!-- wp:group {"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"},"blockGap":"1em"},"border":{"radius":"8px","width":"3px"}},"borderColor":"contrast","textColor":"foreground","layout":{"type":"default"}} -->
		<div class="wp-block-group has-border-color has-contrast-border-color has-foreground-color has-text-color" style="border-width:3px;border-radius:8px;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em"><!-- wp:columns {"isStackedOnMobile":false} -->
		<div class="wp-block-columns is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"top","width":"80%","style":{"spacing":{"blockGap":"0.75em"}}} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:80%"><!-- wp:heading {"level":4,"style":{"typography":{"lineHeight":"1"}}} -->
		<h4 class="wp-block-heading" style="line-height:1"><strong>' . esc_html__( 'Stay up to date', 'omniform' ) . '</strong></h4>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>' . esc_html__( 'Get notified when I publish something new, and unsubscribe at any time.', 'omniform' ) . '</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"20%"} -->
		<div class="wp-block-column" style="flex-basis:20%"></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"1em"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field {"fieldLabel":"' . esc_html__( 'Your email address', 'omniform' ) . '","fieldName":"' . esc_attr__( 'your-email-address', 'omniform' ) . '","isRequired":true,"style":{"layout":{"selfStretch":"fill"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch","verticalAlignment":"center"}} -->
		<!-- wp:omniform/input {"fieldType":"email","fieldPlaceholder":"' . esc_attr__( 'Your email address', 'omniform' ) . '","style":{"spacing":{"padding":{"top":"0.72em","right":"0.72em","bottom":"0.72em","left":"0.72em"}}}} /-->
		<!-- /wp:omniform/field -->

		<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"' . esc_html__( 'Join', 'omniform' ) . '","className":"is-style-fill"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
