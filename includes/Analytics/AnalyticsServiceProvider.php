<?php
/**
 * The AnalyticsServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use OmniForm\Plugin\Schema;

/**
 * The AnalyticsServiceProvider class.
 */
class AnalyticsServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	const EVENTS_TABLE = 'omniform_stats_events';

	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array(
			AnalyticsManager::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->addShared(
			AnalyticsManager::class,
			function () {
				return new AnalyticsManager(
					$this->getContainer()->get( 'db' ),
					$this->generate_daily_salt()
				);
			}
		);
	}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'omniform_activate', array( $this, 'activate' ) );
	}

	/**
	 * Generate the daily salt.
	 *
	 * @return string
	 */
	public function generate_daily_salt() {
		$daily_salt = get_transient( 'omniform_analytics_salt' );

		if ( false === $daily_salt ) {
			$salt       = wp_generate_password( 64, true, true );
			$daily_salt = hash( 'sha256', $salt . gmdate( 'Y-m-d' ) );

			set_transient( 'omniform_analytics_salt', $daily_salt, DAY_IN_SECONDS );
		}

		return $daily_salt;
	}

	/**
	 * Initialize the analytics tables.
	 */
	public function activate() {
		$events_table_definition = array(
			'`event_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`form_id` BIGINT(20) UNSIGNED NOT NULL',
			'`visitor_hash` CHAR(64) NOT NULL',
			'`event_type` TINYINT(1) UNSIGNED NOT NULL',
			'`event_time` DATETIME NOT NULL',
			'PRIMARY KEY (`event_id`)',
			'INDEX (`form_id`)',
			'INDEX (`event_type`)',
		);

		if ( ! Schema::has_table( self::EVENTS_TABLE ) ) {
			Schema::create( self::EVENTS_TABLE, $events_table_definition );
		}
	}
}
