<?php
namespace WpCliBatchProcess;

class DeleteHandler implements RecivesResults {
	public function handle( $results ): bool {
		foreach ( $results as $result ) {
			$deleted = \wp_delete_post( $result->ID, true );
			if( $deleted ){
				return false;
			}
		}
		return true;
	}
}
