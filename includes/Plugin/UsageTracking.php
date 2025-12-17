<?php
/**
 * The UsageTracking class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * The UsageTracking class.
 */
class UsageTracking {

	/**
	 * Retrieves the data for usage tracking.
	 *
	 * @return mixed The data collected for usage tracking.
	 */
	public function get_data() {

		$data = array_merge(
			array(
				'omniform_version' => omniform()->version(),
				'url'              => home_url(),
			),
			$this->get_environment(),
			$this->get_multisite(),
			$this->get_active_theme(),
		);

		return $data;
	}

	/**
	 * Retrieves the environment details.
	 *
	 * @return array An associative array of environment details.
	 */
	private function get_environment(): array {
		global $wpdb;

		return array(
			'mysql_version'  => $wpdb->db_version(),
			'php_version'    => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
			'wp_version'     => get_bloginfo( 'version' ),
			'active_plugins' => $this->get_active_plugins(),
		);
	}

	/**
	 * Retrieves multisite information.
	 *
	 * @return array An associative array containing multisite information.
	 */
	private function get_multisite(): array {
		if ( ! is_multisite() ) {
			return array();
		}

		return array(
			'is_main_site'         => is_main_site(),
			'is_multisite'         => true,
			'is_network_activated' => is_plugin_active_for_network( plugin_basename( omniform()->base_path() ) ),
			'sites_count'          => function_exists( 'get_blog_count' ) ? (int) get_blog_count() : 1,
		);
	}

	/**
	 * Retrieves a list of active plugins.
	 *
	 * @return array An array of active plugins.
	 */
	private function get_active_plugins(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$active_plugins = array();

		foreach ( get_mu_plugins() as $path => $plugin ) {
			$active_plugins[ $path ] = isset( $plugin['Version'] ) ? $plugin['Version'] : '';
		}

		foreach ( get_plugins() as $path => $plugin ) {
			if ( is_plugin_active( $path ) ) {
				$active_plugins[ $path ] = isset( $plugin['Version'] ) ? $plugin['Version'] : '';
			}
		}

		return $active_plugins;
	}

	/**
	 * Retrieves the currently active theme.
	 *
	 * @return string The name or identifier of the active theme.
	 */
	private function get_active_theme(): array {
		$theme = wp_get_theme();

		return array(
			'theme_name'    => $theme->get( 'Name' ),
			'theme_version' => $theme->get( 'Version' ),
		);
	}
}
