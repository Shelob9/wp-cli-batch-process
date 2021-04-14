<?php
/**
 * WP Term Migration CLI commands
 *
 * @package PluginNamespace
 */

namespace PluginNamespace\Commands;

use PluginNamespace\Migration;

/**
 * Gets a namespaced function name.
 *
 * @param  string $function The function name
 * @return string
 */
function n( $function ) {
	return __NAMESPACE__ . "\\$function";
};

/**
 * Setup CLI commands.
 *
 * @return void
 */
function add_commands() {
	\WP_CLI::add_command( 'plugin-name run', n( 'run_command' ) );
	\WP_CLI::add_command( 'plugin-name create-test-data', n( 'create_test_data' ) );
}

/**
 * Gets default results array.
 *
 * @return array
 */
function default_results() {
	return [
		'success'       => false,
		'error_code'    => false,
		'error_message' => false,
	];
}

/**
 *
 *
 *
 * ## OPTIONS
 *
 * <arg>
 * : desc
 *
 * [--optional-arg]
 * : Desc
 *
 * @param array $args       Positional args.
 * @param array $assoc_args Associative args.
 * @return void
 */
function run_command( $args, $assoc_args = [] ) {

	$arg      = $args[0];
	$optional = isset( $assoc_args['optional-arg'] );

	$results = default_results();
	// phpcs:ignore
	if ( rand() ) {
		\WP_CLI::error( 'Error' );
	}

	if ( $results['success'] ) {
		\WP_CLI::success( __( 'Success', 'wp-cli-plugin-name' ) );
	} else {
		\WP_CLI::error( $results['error_code'] . ': ' . $results['error_message'] );
	}
}

/**
 * Creates test data
 *
 * [--cleanup]
 * : Cleanup test data
 *
 * @param array $args       Positional args.
 * @param array $assoc_args Associative args.
 * @return void
 */
function create_test_data( $args, $assoc_args = [] ) {
	\WP_CLI::success( 'All Done' );
}
