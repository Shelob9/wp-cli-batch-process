<?php
/**
 * Plugin Name: Plugin Name
 * Description: 
 * Version:     0.1.0
 * Author:      
 * Author URI:  
 * Plugin URI:  
 * License:     GPLv2 or later
 * Text Domain: plugin-name
 *
 * @package  PluginNamespace
 */

namespace PluginNamespace;

define( 'PLUGIN_NAME_MIGRATION_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PLUGIN_NAME_MIGRATION_VERSION', '0.1.0' );

$files = [
	'helpers.php',
	'migration.php',
	'commands.php',
];

foreach ( $files as $file ) {
	require_once PLUGIN_NAME_MIGRATION_PATH . 'includes/' . $file;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	// Adds the migration CLI Commands.
	\PluginNamespace\Commands\add_commands();
}
