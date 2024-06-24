<?php
/**
 * The AnalyticsServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Analytics;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use OmniForm\Plugin\QueryBuilderFactory;
use OmniForm\Plugin\Schema;

/**
 * The AnalyticsServiceProvider class.
 */
class AnalyticsServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * The database version.
	 *
	 * @var int
	 */
	const DB_VERSION = 1;

	const EVENTS_TABLE  = 'omniform_stats_events';
	const VISITOR_TABLE = 'omniform_stats_visitors';

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
					$this->getContainer()->get( QueryBuilderFactory::class ),
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
		add_action( 'admin_init', array( $this, 'update_database' ) );

		add_action( 'delete_post', array( $this, 'on_delete_form' ), 10, 2 );
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
			'`visitor_id` BIGINT(20) UNSIGNED NOT NULL',
			'`event_type` TINYINT(1) UNSIGNED NOT NULL',
			'`event_time` DATETIME NOT NULL',
			'PRIMARY KEY (`event_id`)',
			'INDEX (`form_id`)',
			'INDEX (`visitor_id`)',
			'INDEX (`event_type`)',
		);

		if ( ! Schema::has_table( self::EVENTS_TABLE ) ) {
			Schema::create( self::EVENTS_TABLE, $events_table_definition );
		}

		$visitors_table_definition = array(
			'`visitor_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
			'`visitor_hash` CHAR(64) NOT NULL',
			'PRIMARY KEY (`visitor_id`)',
		);

		if ( ! Schema::has_table( self::VISITOR_TABLE ) ) {
			Schema::create( self::VISITOR_TABLE, $visitors_table_definition );
		}
	}

	/**
	 * Update the database if needed.
	 */
	public function update_database() {
		$installed_version = get_option( 'omniform_analytics_db_version' );

		// Check if an update is needed.
		if ( false === $installed_version || version_compare( (int) $installed_version, self::DB_VERSION, '<' ) ) {
			$this->activate();

			// Update the installed version number.
			update_option( 'omniform_analytics_db_version', self::DB_VERSION );
		}
	}

	/**
	 * Fires immediately before a post is deleted from the database.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post   Post object.
	 */
	public function on_delete_form( $post_id, $post ) {
		if ( 'omniform' !== $post->post_type ) {
			return;
		}

		$this->getContainer()->get( AnalyticsManager::class )
			->delete_form_data( $post_id );
	}
}
