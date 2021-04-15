<?php
namespace PluginNamespace\Commands;

use PluginNamespace\QueryFromJson;
use PluginNamespace\Helpers;

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
		'completed'     => false,
		'error_code'    => false,
		'error_message' => false,
	];
}

function get_procesors() {
	return apply_filters(
		'plugin_name_get_processors',
		[]
	);
}

function get_processor( string $name ) {
	$processors = get_processor();
	if ( isset( $processors[ $name ] ) ) {
		return $processors[ $name ];
	}
	return false;
}
/**
 *
 *
 *
 * ## OPTIONS
 *
 * <processor>
 * : name of processor
 *
 * [--optional-arg]
 * : Desc
 *
 * @param array $args       Positional args.
 * @param array $assoc_args Associative args.
 * @return void
 */
function run_command( $args, $assoc_args = [] ) {
	$processor_name = $args[0];
	$results        = default_results();
	$processor      = get_processor( $processor_name );
	// phpcs:ignore
	if ( ! $processor ) {
		\WP_CLI::error( sprintf( 'Processor %s not found', $processor_name ) );
	}

	$argsProvider   = new QueryFromJson( $processor[0] );
	$handler        = new $processor[1]();
	$query          = new \WP_Query();
	$processResults = PluginNamespace\Helpers::processWithWpQuery(
		$argsProvider,
		$handler,
		$query
	);

	$results = array_merge( $results, $processResults->toArray() );
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
