<?php

namespace WpCliBatchProcess;



function setUp(){
    $processorsDir =  __DIR__ . '/processors/';
    add_filter( 'wp_cli_batch_process_get_processors', function($processors) use($processorsDir){
        $processors['delete-all-draft-products'] = [
            'type' => 'WP_QUERY',
            'source' => $processorsDir .'all-draft-products.json',
            'handler' => 'WpCliBatchProcess::DeleteHandler'
        ];
        $processors['delete-sample-content'] = [
            'type' => 'CSV',
            'source' => $processorsDir .'hello-world.csv',
            'handler' => 'WpCliBatchProcess::DeleteHandler'
        ];
        return $processors;
    });
}