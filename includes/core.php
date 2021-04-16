<?php

namespace PluginNamespace;



function setUp(){
    $processorsDir =  __DIR__ . '/processors/';
    add_filter( 'plugin_name_get_processors', function($processors) use($processorsDir){
        $processors['delete-all-draft-products'] = [
            'type' => 'WP_QUERY',
            'source' => $processorsDir .'all-draft-products.json',
            'handler' => PluginNamespace::DeleteHandler
        ];
        $processors['delete-sample-content'] = [
            'type' => 'CSV',
            'source' => $processorsDir .'hello-world.csv',
            'handler' => PluginNamespace::DeleteHandler
        ];
        return $processors;
    });
}