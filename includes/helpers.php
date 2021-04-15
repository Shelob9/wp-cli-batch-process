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
