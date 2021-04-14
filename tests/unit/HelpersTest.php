<?php

use PHPUnit\Framework\TestCase;
use PluginNamespace\Helpers;
use PluginNamespace\ProvidesQueryArgs;
use PluginNamespace\RecivesResults;
use PluginNamespace\ProcessResult;
class HelpersTest extends TestCase {
    public function testSucessfulResult(){
        $argsProvider = new class implements ProvidesQueryArgs {
            public function getPage():int{
                return 2;
            }
            public function setPage(int $page): int{}
            public function getArgs(): array{
                return [];
            }
        };
        $handler = new class implements RecivesResults {
            public function handle($results): bool
            {
                return true;
            }
        };
        $query = new \WP_Query();
        $query->max_num_pages = 3;
       $processResult = PluginNamespace\Helpers\process(
            $argsProvider,  
            $handler,
            $query
       );
       $this->assertTrue( $processResult->success );
       $this->assertFalse($processResult->complete );
       
    }
}