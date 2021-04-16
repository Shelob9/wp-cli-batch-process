<?php
namespace WpCliBatchProcess;

class QueryFromJson implements ProvidesQueryArgs {

	protected $page;
	protected $path;
	public function __construct( string $path ) {
		$this->path = $path;
	}

	public function getPage(): int {
		return $this->page;
	}
	public function setPage( int $page ): int {
		$this->page = $page;
		return $this->page;
	}
	public function getArgs(): array {
			return json_decode(
				file_get_contents( $this->path ),
				true
			)
	}
}
