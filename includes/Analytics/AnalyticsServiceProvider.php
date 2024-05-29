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
	const EVENTS_TABLE  = 'omniform_stats_events';
	const SOURCES_TABLE = 'omniform_stats_sources';
	const TARGETS_TABLE = 'omniform_stats_targets';

	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array(
			// AnalyticsManager::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		// $this->getContainer()->addShared( AnalyticsManager::class );
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
	 * Initialize the analytics tables.
	 */
	public function activate() {
		global $wpdb;

		$events_table_definition = array(
			'`event_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`form_id` BIGINT(20) UNSIGNED NOT NULL',
			'`visitor_hash` CHAR(64) NOT NULL',
			'`event_type` TINYINT(1) UNSIGNED NOT NULL',
			'`event_time` DATETIME NOT NULL',
			'`source_id` BIGINT(20) UNSIGNED',
			'`target_id` BIGINT(20) UNSIGNED',
			'PRIMARY KEY (`event_id`)',
			'INDEX (`form_id`)',
			'INDEX (`event_type`)',
			'INDEX (`source_id`)',
			'INDEX (`target_id`)',
			'FOREIGN KEY (`source_id`) REFERENCES `' . $wpdb->prefix . self::SOURCES_TABLE . '`(`source_id`)',
			'FOREIGN KEY (`target_id`) REFERENCES `' . $wpdb->prefix . self::TARGETS_TABLE . '`(`target_id`)',
		);

		$sources_table_definition = array(
			'`source_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`source_url` VARCHAR(255) NOT NULL',
			'`source_domain` VARCHAR(255) NOT NULL',
			'PRIMARY KEY (`source_id`)',
			'UNIQUE INDEX (`source_domain`)',
		);

		$targets_table_definition = array(
			'`target_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`target_url` VARCHAR(255) NOT NULL',
			'`target_path` VARCHAR(255) NOT NULL',
			'PRIMARY KEY (`target_id`)',
			'UNIQUE INDEX (`target_path`)',
		);

		if ( ! Schema::has_table( self::SOURCES_TABLE ) ) {
			Schema::create( self::SOURCES_TABLE, $sources_table_definition );
		}

		if ( ! Schema::has_table( self::TARGETS_TABLE ) ) {
			Schema::create( self::TARGETS_TABLE, $targets_table_definition );
		}

		if ( ! Schema::has_table( self::EVENTS_TABLE ) ) {
			Schema::create( self::EVENTS_TABLE, $events_table_definition );
		}
	}
}
