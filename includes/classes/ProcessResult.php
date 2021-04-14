<?php
namespace PluginNamespace;

class ProcessResult {
	public $sucess;
	public $complete;

	public function __construct( bool $complete ) {
		$this->complete = $complete;
	}

	public function toArray():array {
		return [
			'sucess'   => isset( $this->sucess ) ? $this->sucess : false,
			'complete' => $this->complete,
		];
	}
}
