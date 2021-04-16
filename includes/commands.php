<?php
namespace WpCliBatchProcess\Commands;

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

function get_procesors() {
	return apply_filters(
		'wp_cli_batch_process_get_processors',
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

function process_batch(string $processor_name, int $page, int $perPage){
	$processResult = WP_CLI::runcommand( "plugin-name run $processor_name --page=$page --per-page=$perPage", $options );
	if( ! $processResult->success ){
		\WP_CLI::error( sprintf( 'Processor %s made an error on page %s', $processor_name, $page) );
	}elseif( ! $processResult->completed ){
		return process_batch($processor_name,$page + 1,$perPage);
	}else{
		\WP_CLI::success( __( 'Success', 'wp-cli-plugin-name' ) );
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
function run_batch_command(){
	$processor_name = $args[0];
	$results        = default_results();
	$processor      = get_processor( $processor_name );
	// phpcs:ignore
	if ( ! $processor ) {
		\WP_CLI::error( sprintf( 'Processor %s not found', $processor_name ) );
	}
	$perPage = (int)$args['perpage'] ? $args['perpage'] : 25;
	$page = 1;
	
	$runCommandOptions =[
		'return'     => true,   // Return 'STDOUT'; use 'all' for full object.
		'parse'      => 'json', // Parse captured STDOUT to JSON array.
		'launch'     => true,  // Reuse the current process.
		'exit_error' => true,   // Halt script execution on error.
	];
	process_batch($processor_name,$page,$perPage);
	
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
* [--page]
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

	$page =(int)$args['page'] ? $args['page'] : 25;
	$perPage =(int) $args['perpage'] ? $args['perpage'] : 25;


	//@todo extract this switch to a function and test
	switch( $processor['type'] ){
		case 'WP_Query':
			$argsProvider   = new QueryFromJson( $processor['source'] );
			$argsProvider->setPage($page);
			$handler        = new $processor['handler']();
			$query          = new \WP_Query();
			$processResults = WpCliBatchProcess\Helpers::processWithWpQuery(
				$argsProvider,
				$handler,
				$query
			);
			break;
		case 'CSV':
			$handler        = new $processor['handler']();
			$processResults =  WpCliBatchProcess\Helpers::processFromCsv(
					$processor['source'],
					$page,
					$perPage,
					$handler
			);
			break;
			default: 
			throw new \Exception( 'Invalid handler' );
	}
	

	$results = array_merge( $results, $processResults->toArray() );
	if ( $results['success'] ) {
		\WP_CLI::success( __( 'Success', 'wp-cli-plugin-name' ) );
	} else {
		\WP_CLI::error( $results['error_code'] . ': ' . $results['error_message'] );
	}
}
