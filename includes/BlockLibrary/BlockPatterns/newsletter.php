<?php
/**
 * The "Newsletter" form block pattern.
 *
 * @package OmniForm
 */

return array(
	'title'   => __( 'Newsletter', 'omniform' ),
	'content' => '
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}},"border":{"radius":"8px","width":"2px"}},"textColor":"foreground","layout":{"type":"default"}} -->
		<div class="wp-block-group has-foreground-color has-text-color" style="border-width:2px;border-radius:8px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"isStackedOnMobile":false} -->
		<div class="wp-block-columns is-not-stacked-on-mobile"><!-- wp:column {"verticalAlignment":"top","width":"66.66%","style":{"spacing":{"blockGap":"0"}}} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:66.66%"><!-- wp:paragraph -->
		<p><strong>Stay up to date</strong></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p>Get notified when I publish something new, and unsubscribe at any time.</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"33.33%"} -->
		<div class="wp-block-column" style="flex-basis:33.33%"></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"1.5em"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"bottom"}} -->
		<div class="wp-block-group"><!-- wp:omniform/field-input {"fieldType":"email","fieldPlaceholder":"","fieldLabel":"Your email address","fieldName":"your-email-address","isRequired":false} /-->

		<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Join"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
	',
);
