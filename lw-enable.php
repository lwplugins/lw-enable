<?php
/**
 * Plugin Name:       Lightweight Enable
 * Plugin URI:        https://github.com/lwplugins/lw-enable
 * Description:       Enable WordPress features: SVG uploads and more.
 * Version:           1.0.2
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            LW Plugins
 * Author URI:        https://lwplugins.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lw-enable
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LW_ENABLE_VERSION', '1.0.2' );
define( 'LW_ENABLE_FILE', __FILE__ );
define( 'LW_ENABLE_PATH', plugin_dir_path( __FILE__ ) );
define( 'LW_ENABLE_URL', plugin_dir_url( __FILE__ ) );

// Autoloader.
if ( file_exists( LW_ENABLE_PATH . 'vendor/autoload.php' ) ) {
	require_once LW_ENABLE_PATH . 'vendor/autoload.php';
}

/**
 * Initialize plugin.
 *
 * @return Plugin
 */
function lw_enable(): Plugin {
	static $instance = null;

	if ( null === $instance ) {
		$instance = new Plugin();
	}

	return $instance;
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\lw_enable' );
