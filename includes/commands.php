<?php
namespace WpCliBatchProcess\Commands;

use WpCliBatchProcess\DeleteHandler;
use WpCliBatchProcess\QueryFromJson;
use WpCliBatchProcess\Helpers;

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
	\WP_CLI::add_command( 'batch-process run', n( 'run_command' ) );
	\WP_CLI::add_command( 'batch-process batch', n( 'run_batch_command' ) );
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



function get_processor( string $name ) {
	$processors = apply_filters(
		'wp_cli_batch_process_get_processors',
		[]
	);
	if ( isset( $processors[ $name ] ) ) {
		return $processors[ $name ];
	}
	return false;
}

function process_batch( string $processor_name, int $page, int $perPage, array $options ) {
	\WP_CLI::line( sprintf('Staring page %d',$page));
	$commandResult = \WP_CLI::launch_self( 
		"batch-process run $processor_name --page=$page --per-page=$perPage --exitOnComplete=true",
		 $options,
	);

	if ( ! $commandResult ) {
		return process_batch( $processor_name, $page + 1, $perPage, $options );
	} else {
		\WP_CLI::success( 
			__( 'Success', 'wp-cli-plugin-name' )
		);
	}
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
 * [--perpage]
 * : Desc
 *
 * @param array $args       Positional args.
 * @param array $assoc_args Associative args.
 * @return void
 */
function run_batch_command( $args, $assoc_args = [] ) {

	$processor_name = $args[0];
	$results        = default_results();
	$processor      = get_processor( $processor_name );
	// phpcs:ignore
	if ( ! $processor ) {
		\WP_CLI::error( sprintf( 'Processor %s not found', $processor_name ) );
	}
	$perPage = (int) $args['perpage'] ? $args['perpage'] : 25;
	$page    = 1;

	$runCommandOptions = [
		'return'     => true,   // Return 'STDOUT'; use 'all' for full object.
		'parse'      => 'json', // Parse captured STDOUT to JSON array.
		'launch'     => true,  // Reuse the current process.
		'exit_error' => true,   // Halt script execution on error.
	];
	// Process recurisvely until complete or error
	process_batch( $processor_name, $page, $perPage, $runCommandOptions );

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
 * [--perpage]
 * : Posts per page of query
 *
 * [--page]
 * : Page of query
 * 
 * [--exitOnComplete]
 * : Return error when completed if true. Default is false.
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
		return;
	}
	$page           = isset($args['page']) ? (int)$args['page'] : 1;
	$perPage        = isset($args['perpage']) ? (int)$args['perpage'] : 25;
	$exitOnComplete        = isset($args['exitOnComplete']) ?
		(bool) $args['exitOnComplete'] : false; 
	try {
		$processResults = \WpCliBatchProcess\Helpers\processRun(
			$page,
			$perPage,
			$processor
		);
		
	} catch (\Throwable $th) {
		 \WP_CLI::error( $th->__toString() );
		 return;
	}
	

	$results = array_merge( $results, $processResults->toArray() );
	// @todo deal with typo in ProcessResults
	if ( $results['success'] || $results['sucess'] ) {
		\WP_CLI::success( __( 'Success', 'wp-cli-plugin-name' ) );
		if( $exitOnComplete && $processResults->complete ){
			\WP_CLI::error( 'Completed' );
		}
	} else {
		\WP_CLI::error( $results['error_code'] . ': ' . $results['error_message'] );
	}
}
