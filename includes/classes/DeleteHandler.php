<?php
namespace WpCliBatchProcess;

/**
 * Default deleter
 */
class DeleteHandler implements RecivesResults {
	public function handle( $results ): bool {
		foreach ( $results as $result ) {
			\wp_delete_post( $result->ID, true );

		}
		return true;
	}
}
