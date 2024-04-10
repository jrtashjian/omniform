<?php
/**
 * Plugin Name: OmniForm
 * Plugin URI: https://omniform.io
 * Description: Easily create and manage custom forms with the block editor, customizable fields, and form submission management for your website.
 * Version: 1.2.1
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: JR Tashjian
 * Author URI: https://jrtashjian.com
 * Text Domain: omniform
 *
 * Copyright 2022-2023 OmniForm
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

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Create and retrieve the main application container instance.
 *
 * @return \OmniForm\Application The application container.
 */
function omniform() {
	return \OmniForm\Application::get_instance();
}

omniform()->set_base_path( __FILE__ );

/**
 * Service Providers.
 */
omniform()->addServiceProvider( new \OmniForm\Plugin\PluginServiceProvider() );
omniform()->addServiceProvider( new \OmniForm\BlockLibrary\BlockLibraryServiceProvider() );

register_activation_hook( __FILE__, array( omniform(), 'activation' ) );
register_deactivation_hook( __FILE__, array( omniform(), 'deactivation' ) );
