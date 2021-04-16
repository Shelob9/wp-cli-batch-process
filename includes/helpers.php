<?php

namespace WpCliBatchProcess\Helpers;

use WpCliBatchProcess\ProvidesQueryArgs;
use WpCliBatchProcess\RecivesResults;
use WpCliBatchProcess\ProcessResult;

/**
 * Handler for CLI commands that use WP_Query for input
 */
function processWithWpQuery( ProvidesQueryArgs $queryArgProvider, RecivesResults $resultHandler, \WP_Query $query ):ProcessResult {
	$args = $queryArgProvider->getArgs();
	$query->parse_query(
		array_merge(
			$args,
			[
				'paged' => $queryArgProvider->getPage(),
			]
		)
	);

	$results                = $query->get_posts();
	$processResult          = new ProcessResult( $query->max_num_pages <= $queryArgProvider->getPage() );
	$processResult->success = $resultHandler->handle( $results );
	return $processResult;
}

/**
 * Handler for CLI commands that use CSV file as input.
 */
function processFromCsv( string $filePath, int $page, int $perPage, RecivesResults $resultHandler ) {
	$total = getCsvSize($filePath);
	$start = ($page * $perPage)+ 1;
	$end = $start + ($page * $perPage);
	$processResult = new ProcessResult( $end >= $total );
	$rows = getRowsFromCsv($filePath,$start,$end);
	$processResult->success = $resultHandler->handle( $rows );
	return $processResult;

}

/**
 * Get total rows, not including headers in a csv file.
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
 * @param int $start First row.
 * @param int $end Last row.
 */
function getRowsFromCsv(string $filePath, int $start, int $end)
{
	$handle = fopen($filePath, "r");
	$count = getCsvSize($filePath);
	$lineNumber = 0;
	$rows = [];
	while (($raw_string = fgets($handle)) !== false) {
		if( $lineNumber >= $start  ){
				$rows[] = str_getcsv($raw_string);
		}
		$lineNumber++;
		if( $lineNumber === $end ){
			break;
		}

	}
	return $rows;
}
