<?php
namespace WpCliBatchProcess;

class QueryFromJson extends AbstractArgProvider {

	
	protected $path;
	/**
	 * Path to json query
	 *
	 * @param string $path
	 */
	public function __construct( string $path ) {
		$this->path = $path;
	}
	
	public function getArgs(): array {
		$args = json_decode(
			file_get_contents( $this->path ),
			true
		);
		return $this->mergeArgs($args);
	}
}
