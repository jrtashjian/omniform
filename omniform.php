<?php
/**
 * Plugin Name: OmniForm
 * Plugin URI: https://omniformwp.com
 * Description: OmniForm Plugin Description.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: JR Tashjian
 * Author URI: https://jrtashjian.com
 * Text Domain: omniform
 * Domain Path: /languages
 *
 * Copyright 2019-2022 OmniForm
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package OmniForm
 */

defined( 'ABSPATH' ) || exit;

// Guard the plugin from initializing more than once.
if ( class_exists( \OmniForm\Application::class ) ) {
	return;
}

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Create and retrieve the main application container instance.
 *
 * @return \OmniForm\Application The application container.
 */
function omniform() {
	return \OmniForm\Application::getInstance();
}

omniform()->setBasePath( __FILE__ );

/**
 * Service Providers.
 */
omniform()->addServiceProvider( new \OmniForm\Plugin\PluginServiceProvider() );
omniform()->addServiceProvider( new \OmniForm\BlockLibrary\BlockLibraryServiceProvider() );

register_activation_hook( __FILE__, array( omniform(), 'activation' ) );
register_deactivation_hook( __FILE__, array( omniform(), 'deactivation' ) );
add_action( 'plugins_loaded', array( omniform(), 'loadTextDomain' ) );
