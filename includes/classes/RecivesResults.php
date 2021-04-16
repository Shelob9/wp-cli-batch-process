<?php
namespace WpCliBatchProcess;

interface RecivesResults {
	public function handle( $results):bool;
}
