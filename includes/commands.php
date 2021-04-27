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


/**
 * Find processor, by name.
 *
 * @param string $name
 * @return array|false
 */
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

function process_batch( $args, $progress ) {
	$page = $args['page'];
	\WP_CLI::line( sprintf( 'Starting page %d', $page ) );
	$processResults = run_command( $args, [] );
	if ( $processResults->complete ) {
		$progress->finish();
		\WP_CLI::success( 'Completed processing.' );
	} else {
		$args['page'] = $args['page'] + 1;
		$progress->tick();
		process_batch( $args, $progress );
	}

}
/**
 * Batch command
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
	$processor      = get_processor( $processor_name );
	// phpcs:ignore
	if ( ! $processor ) {
		\WP_CLI::error( sprintf( 'Processor %s not found', $processor_name ) );
	}
	$args['perpage'] = (int) $args['perpage'] ? $args['perpage'] : 25;
	$args['page']    = 1;
	$args['quiet']   = true;

	$progress = \WP_CLI\Utils\make_progress_bar( sprintf( 'Starting batch process %s', $processor_name ), 100 );

	// Process recurisvely until complete or error
	process_batch( $args, $progress );
}
/**
 * Single page command
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
 * [--quiet]
 * : No output. Default is false.
 *
 * @param array $args       Positional args.
 * @param array $assoc_args Associative args.
 * @return \WpCliBatchProcess\ProcessResult
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
	$quiet   = isset( $args['quiet'] ) ? $args['quiet'] : false;
	$page    = isset( $args['page'] ) ? (int) $args['page'] : 1;
	$perPage = isset( $args['perpage'] ) ? (int) $args['perpage'] : 25;

	try {
		$processResults = \WpCliBatchProcess\Helpers\processRun(
			$page,
			$perPage,
			$processor
		);

	} catch ( \Throwable $th ) {
		if ( $quiet ) {
			throw $th;
		}
		 \WP_CLI::error( $th->__toString() );
		 return;
	}
	if ( $quiet ) {
		return $processResults;
	}
	\WP_CLI::line( sprintf( 'Page %d: process %s complete', $page, $processResults->complete ? 'is' : 'is not' ) );

	if ( $processResults->wasSuccess() ) {
		if ( $processResults->complete ) {
			\WP_CLI::halt( 'Completed Run.' );
		} else {
			\WP_CLI::success( sprintf( 'Completed page %d', $page ) );
		}
	} else {
		\WP_CLI::error( sprintf( 'Error on page %d', $page ) );
	}
	return $processResults;
}
