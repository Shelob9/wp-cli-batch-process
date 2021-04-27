<?php

namespace WpCliBatchProcess\Helpers;

use WpCliBatchProcess\DeleteHandler;
use WpCliBatchProcess\ProvidesQueryArgs;
use WpCliBatchProcess\RecivesResults;
use WpCliBatchProcess\ProcessResult;
use WpCliBatchProcess\QueryFromJson;

/**
 * Handler for CLI commands that use WP_Query for input
 */
function processWithWpQuery( ProvidesQueryArgs $queryArgProvider, RecivesResults $resultHandler, \WP_Query $query ):ProcessResult {
	$query->parse_query(
		$queryArgProvider->getArgs()
	);
	$results                = $query->get_posts();
	$processResult          = new ProcessResult(
		$query->max_num_pages <= $queryArgProvider->getPage()
	);
	$processResult->success = $resultHandler->handle( $results );
	return $processResult;
}

/**
 * Handler for CLI commands that use WP_Query and delete
 */
function processWithWpQueryAndDelete( ProvidesQueryArgs $queryArgProvider, RecivesResults $resultHandler, \WP_Query $query ):ProcessResult {
	$args          = $queryArgProvider->getArgs();
	$args['paged'] = 1;// always 1 for deletes
	$query->parse_query(
		$args
	);
	$results = $query->get_posts();

	$processResult = new ProcessResult(
		// Delete until on last page
		$query->max_num_pages == 1
	);
   	if ( $query->post_count <= 0 ) {
		$processResult->complete = true;
	}
	$processResult->success = $resultHandler->handle( $results );
	return $processResult;

}

/**
 * Handler for CLI commands that use CSV file as input.
 */
function processFromCsv( string $filePath, int $page, int $perPage, RecivesResults $resultHandler ) {
	$total                  = getCsvSize( $filePath );
	$start                  = ( $page * $perPage ) + 1;
	$end                    = $start + ( $page * $perPage );
	$processResult          = new ProcessResult( $end >= $total );
	$rows                   = getRowsFromCsv( $filePath, $start, $end );
	$processResult->success = $resultHandler->handle( $rows );
	return $processResult;

}

/**
 * Get total rows, not including headers in a csv file.
 *
 * @param string $filePath Path to file, including extension.
 */
function getCsvSize( string $filePath ) : int {
	$file = new \SplFileObject( $filePath, 'r' );
	$file->seek( PHP_INT_MAX );
	return $file->key();
}

/**
 * Get a range of rows from a csv file.
 *
 * @param string $filePath Path to file, including extension.
 * @param int    $start First row.
 * @param int    $end Last row.
 */
function getRowsFromCsv( string $filePath, int $start, int $end ) {
	$handle     = fopen( $filePath, 'r' );
	$count      = getCsvSize( $filePath );
	$lineNumber = 0;
	$rows       = [];
	while ( ( $raw_string = fgets( $handle ) ) !== false ) {
		if ( $lineNumber >= $start ) {
				$rows[] = str_getcsv( $raw_string );
		}
		$lineNumber++;
		if ( $lineNumber === $end ) {
			break;
		}
	}
	return $rows;
}

/**
 * Undocumented function
 *
 * @param integer $page
 * @param integer $perPage
 * @param array   $processor
 * @return \WpCliBatchProcess\ProcessResult
 */
function processRun( int $page, int $perPage, array $processor ) {
	$isDefaultDelete = is_string( $processor['handler'] ) && in_array(
		$processor['handler'],
		[
			'WpCliBatchProcess::DeleteHandler',
		]
	);
	$handler         = $isDefaultDelete ? new DeleteHandler() : new $processor['handler']();

	switch ( $processor['type'] ) {
		case 'WP_QUERY':
		case 'WP_Query':
			$argsProvider = new QueryFromJson( $processor['source'] );
			$argsProvider->setPage( $page );
			$argsProvider->setPerPage( $perPage );
			$query = new \WP_Query();
			if ( $isDefaultDelete ) {
				$processResults = \WpCliBatchProcess\Helpers\processWithWpQueryAndDelete(
					$argsProvider,
					$handler,
					$query
				);
			} else {
				$processResults = \WpCliBatchProcess\Helpers\processWithWpQuery(
					$argsProvider,
					$handler,
					$query
				);
			}

			break;
		case 'CSV':
			$processResults = \WpCliBatchProcess\Helpers\processFromCsv(
				$processor['source'],
				$page,
				$perPage,
				$handler
			);
			break;
		default:
			throw new \Exception( 'Invalid processor' );
	}
	return $processResults;
}
