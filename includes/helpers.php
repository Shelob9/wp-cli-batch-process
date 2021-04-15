<?php
/**
 * WP Term Migration Helpers
 *
 * @package PluginNamespace
 */

namespace PluginNamespace\Helpers;

use PluginNamespace\ProvidesQueryArgs;
use PluginNamespace\RecivesResults;
use PluginNamespace\ProcessResult;
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

function processFromCsv( string $path, int $page, RecivesResults $resultHandler ) {

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
	$count = PluginNamespace\Helpers\getCsvSize($filePath);
	$lineNumber = 0;
	$rows = [];
	while (($raw_string = fgets($handle)) !== false) {
		if( $lineNumber >= $start  ){
				$rows[] = str_getcsv($raw_string);
		}
		$lineNumber++;
		if( $lineNumber > $end ){
			break;
		}
	}
	return $rows;
}
