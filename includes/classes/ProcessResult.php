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
		if( isset( $this->sucess ) ){
			return isset( $this->sucess );
		}
		if( isset( $this->success ) ){
			return isset( $this->success );
		}
		return false;
	}
	public function toArray():array {
		return [
			'sucess'   => isset( $this->sucess ) ? $this->sucess : false,
			'success' => isset( $this->sucess ) ? $this->sucess : false,
			'complete' => $this->complete,
		];
	}
}
