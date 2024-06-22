<?php
/**
 * The FormTypesServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\FormTypes;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * The FormTypesServiceProvider class.
 */
class FormTypesServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array(
			FormTypesManager::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->addShared( FormTypesManager::class );
	}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_default_form_types' ) );

		add_filter( 'block_editor_settings_all', array( $this, 'register_block_editor_settings' ), 10, 2 );
	}

	/**
	 * Register the omniform_type taxonomy.
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => _x( 'OmniForm Type', 'taxonomy general name', 'omniform' ),
			'singular_name' => _x( 'OmniForm Type', 'taxonomy singular name', 'omniform' ),
		);

		$args = array(
			'public'       => false,
			'labels'       => $labels,
			'query_var'    => false,
			'rewrite'      => false,
			'show_in_rest' => false,
			'default'      => array(
				'slug'        => 'standard',
				'name'        => __( 'Standard', 'omniform' ),
				'description' => __( 'A standard form.', 'omniform' ),
			),
		);

		register_taxonomy( 'omniform_type', array( 'omniform' ), $args );
	}

	/**
	 * Register the default form types.
	 */
	public function register_default_form_types() {
		$form_types_manager = $this->getContainer()->get( FormTypesManager::class );

		$form_types_manager->add_form_type(
			array(
				'type'        => 'custom',
				'label'       => __( 'Custom', 'omniform' ),
				'description' => __( 'A custom form.', 'omniform' ),
				'icon'        => '',
			)
		);

		/**
		 * Fires after the default form types have been registered.
		 *
		 * @param FormTypesManager $form_types_manager The form types manager.
		 */
		do_action( 'omniform_register_form_types', $form_types_manager );

		// Insert the default form type terms.
		foreach ( $form_types_manager->get_form_types() as $form_type ) {
			wp_insert_term( $form_type['label'], 'omniform_type' );
		}
	}

	/**
	 * Register the block editor settings.
	 *
	 * @param array                   $editor_settings      Default editor settings.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	public function register_block_editor_settings( $editor_settings, $block_editor_context ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$editor_settings['omniformFormTypes'] = $this->getContainer()->get( FormTypesManager::class )->get_form_types();

		return $editor_settings;
	}
}
