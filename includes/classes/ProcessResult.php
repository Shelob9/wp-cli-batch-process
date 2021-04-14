<?php
namespace PluginNamespace;

class ProcessResult {
    public $sucess;
    public $complete;

    public function __construct(bool $complete ){
        $this->complete = $complete;
    }
}