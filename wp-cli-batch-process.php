<?php
/**
 * Plugin Name: WordPress CLI Batch Process
 * Description: 
 * Version:     0.1.0
 * Author:      
 * Author URI:  
 * Plugin URI:  
 * License:     GPLv2 or later
 * Text Domain: plugin-name
 *
 */

define( 'PLUGIN_NAME_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PLUGIN_NAME_VERSION', '0.1.0' );

require_once PLUGIN_NAME_PATH . '/vendor/autoload.php';
$files = [
	'helpers.php',
	'commands.php',
	'core.php'
];

foreach ( $files as $file ) {
	require_once PLUGIN_NAME_PATH . 'includes/' . $file;
}

\WpCliBatchProcess\setup();
// Add the CLI Commands.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WpCliBatchProcess\Commands\add_commands();
}