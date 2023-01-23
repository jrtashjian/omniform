<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use WP_Query;

/**
 * The BlockLibraryServiceProvider class.
 */
class BlockLibraryServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array();

		return in_array( $id, $services );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'registerBlocks' ) );
		add_action( 'init', array( $this, 'registerPatterns' ) );
		add_filter( 'block_categories_all', array( $this, 'registerCategories' ) );
	}

	/**
	 * Register the blocks.
	 */
	public function registerBlocks() {
		$blocks = array(
			Blocks\Button::class,
			Blocks\FieldInput::class,
			Blocks\FieldSelect::class,
			Blocks\SelectOption::class,
			Blocks\SelectGroup::class,
			Blocks\FieldTextarea::class,
			Blocks\Form::class,
			Blocks\Fieldset::class,
		);

		foreach ( $blocks as $block ) {
			$block_object = new $block();

			$variations = array();

			if ( Blocks\Form::class === $block ) {
				$wp_query_args   = array(
					'post_status'    => array( 'draft', 'publish' ),
					'post_type'      => 'omniform',
					'posts_per_page' => -1,
					'no_found_rows'  => true,
				);
				$variation_query = new WP_Query( $wp_query_args );

				foreach ( $variation_query->posts as $post ) {
					$variations[] = array(
						'name'       => 'omniform//' . $post->post_name,
						'title'      => $post->post_title,
						'attributes' => array(
							'ref' => $post->ID,
						),
						'scope'      => array( 'inserter', 'transform' ),
						'example'    => array(
							'attributes' => array(
								'ref' => $post->ID,
							),
						),
					);
				}
			}

			wp_reset_postdata();

			register_block_type(
				$block_object->blockTypeMetadata(),
				array(
					'render_callback' => array( $block_object, 'renderBlock' ),
					'variations'      => $variations,
				)
			);
		}
	}

	/**
	 * Registers the form block patterns.
	 */
	public function registerPatterns() {
		register_block_pattern_category(
			'forms',
			array( 'label' => __( 'Forms', 'omniform' ) )
		);

		$pattern_defaults = array(
			'postTypes'     => array( 'omniform' ),
			'blockTypes'    => array( 'omniform/form' ),
			'categories'    => array( 'forms' ),
			'viewportWidth' => 640,
		);

		register_block_pattern(
			'omniform/contact-form',
			wp_parse_args(
				array(
					'title'   => __( 'Contact', 'omniform' ),
					'content' => '<!-- wp:heading -->
					<h2>Contact Us</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>If you have any questions or comments, or if you\'d like to work with me or collaborate on a project, please don\'t hesitate to get in touch. I look forward to hearing from you!</p>
					<!-- /wp:paragraph -->

					<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Your email address","fieldName":"your-email-address","isRequired":false} /-->

					<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","fieldName":"your-message","isRequired":false} /-->

					<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /-->',
				),
				$pattern_defaults
			)
		);

		register_block_pattern(
			'omniform/newsletter-form',
			wp_parse_args(
				array(
					'title'   => __( 'Newsletter Signup', 'omniform' ),
					'content' => '<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}},"border":{"radius":"8px","width":"2px"}},"textColor":"foreground","layout":{"type":"default"}} -->
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
					<!-- /wp:group -->',
				),
				$pattern_defaults
			)
		);

		register_block_pattern(
			'omniform/rsvp-form',
			wp_parse_args(
				array(
					'title'   => __( 'RSVP', 'omniform' ),
					'content' => '<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
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
					<!-- /wp:group -->',
				),
				$pattern_defaults
			)
		);

		register_block_pattern(
			'omniform/registration-form',
			wp_parse_args(
				array(
					'title'   => __( 'Membership Registration', 'omniform' ),
					'content' => '<!-- wp:heading -->
					<h2>Membership Registration</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>Join our community and take advantage of our member perks! Sign up for a membership and unlock access to special features and discounts.</p>
					<!-- /wp:paragraph -->

					<!-- wp:omniform/field-input {"fieldLabel":"What\'s your name?","fieldName":"whats-your-name"} /-->

					<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"What\'s your email address?","fieldName":"whats-your-email-address"} /-->

					<!-- wp:omniform/field-input {"fieldType":"tel","fieldLabel":"What\'s your phone number?","fieldName":"whats-your-phone-number"} /-->

					<!-- wp:omniform/field-select {"fieldPlaceholder":" ","fieldLabel":"How did you hear about us?","fieldName":"how-did-you-hear-about-us"} -->
					<!-- wp:omniform/select-option {"fieldLabel":"Referral from a friend or colleague"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Social media"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Online search"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Advertising"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Trade show or event"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Email or newsletter"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Radio or TV"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Print"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Other"} /-->
					<!-- /wp:omniform/field-select -->

					<!-- wp:omniform/field-textarea {"fieldLabel":"Why do you want to be a member?","fieldName":"why-do-you-want-to-be-a-member"} /-->

					<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
					<div class="wp-block-group"><!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send"} /--></div>
					<!-- /wp:group -->',
				),
				$pattern_defaults
			)
		);

		register_block_pattern(
			'omniform/appointment-form',
			wp_parse_args(
				array(
					'title'   => __( 'Appointment', 'omniform' ),
					'content' => '<!-- wp:heading {"className":"wp-block-heading"} -->
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
					<!-- /wp:group -->',
				),
				$pattern_defaults
			)
		);

		register_block_pattern(
			'omniform/feedback-form',
			wp_parse_args(
				array(
					'title'   => __( 'Feedback', 'omniform' ),
					'content' => '<!-- wp:heading -->
					<h2>Feedback</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>We value your feedback! Please take a moment to fill out our website feedback form to let us know how we can improve your experience. Your input is important to us and we appreciate your time. Thank you for visiting our website.</p>
					<!-- /wp:paragraph -->

					<!-- wp:omniform/field-input {"fieldLabel":"Name","fieldName":"name"} /-->

					<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Email","fieldName":"email"} /-->

					<!-- wp:omniform/field-select {"fieldLabel":"Is this your first time visiting our site?","fieldName":"is-this-your-first-time-visiting-our-site"} -->
					<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
					<!-- /wp:omniform/field-select -->

					<!-- wp:omniform/field-select {"fieldLabel":"Did you find what you were looking for?","fieldName":"did-you-find-what-you-were-looking-for"} -->
					<!-- wp:omniform/select-option {"fieldLabel":"No"} /-->

					<!-- wp:omniform/select-option {"fieldLabel":"Yes"} /-->
					<!-- /wp:omniform/field-select -->

					<!-- wp:omniform/fieldset {"fieldLabel":"Please rate our website","fieldName":"please-rate-our-website","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
					<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"1 - Very Bad","fieldName":"1-very-bad"} /-->

					<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"2 - Poor","fieldName":"2-poor"} /-->

					<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"3 - Average","fieldName":"3-average"} /-->

					<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"4 - Good","fieldName":"4-good","isRequired":false} /-->

					<!-- wp:omniform/field-input {"fieldType":"radio","fieldLabel":"5 - Excellent","fieldName":"5-excellent","isRequired":false} /-->
					<!-- /wp:omniform/fieldset -->

					<!-- wp:omniform/field-textarea {"fieldLabel":"How could we improve?","fieldName":"how-could-we-improve"} /-->

					<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Feedback"} /-->',
				),
				$pattern_defaults
			)
		);
	}

	/**
	 * Filters the default array of categories for block types.
	 *
	 * @param array[] $block_categories     Array of categories for block types.
	 */
	public function registerCategories( $block_categories ) {

		$block_categories[] = array(
			'slug'  => 'omniform',
			'title' => __( 'Forms', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-control-simple',
			'title' => __( 'Simple Controls', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-control-group',
			'title' => __( 'Grouped Controls', 'omniform' ),
			'icon'  => null,
		);

		return $block_categories;
	}
}
