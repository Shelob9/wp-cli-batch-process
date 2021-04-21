<?php
namespace WpCliBatchProcess;

class ProcessResult {
	public $sucess;
	public $complete;

	public function __construct( bool $complete ) {
		$this->complete = $complete;
	}

	
	//shim to deal with a typo
	public function wasSuccess() {
		if( isset( $this->success ) ){
			return $this->success;
		}
		return false;
	}
	public function toArray():array {
		return [
			'sucess'   => $this->wasSuccess(),
			'success' => $this->wasSuccess(),
			'complete' => $this->complete,
		];
	}
}
