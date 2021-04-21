<?php

use PHPUnit\Framework\TestCase;
use WpCliBatchProcess\AbstractArgProvider;
use WpCliBatchProcess\Helpers;
use WpCliBatchProcess\ProvidesQueryArgs;
use WpCliBatchProcess\RecivesResults;
use WpCliBatchProcess\ProcessResult;
class HelpersTest extends TestCase {
    public function testSucessfulResult(){
        $argsProvider = new class extends AbstractArgProvider {
            public function getPage():int{
                return 2;
            }
      
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
        $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            $query
       );
       $this->assertTrue( $processResult->wasSuccess() );
       $this->assertFalse($processResult->complete );
    }
    public function testCompleteResult(){
        $argsProvider = new class extends AbstractArgProvider {
            public function getPage():int{
                return 2;
            }
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
        $query->max_num_pages = 2;
        $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            $query
       );
       $this->assertTrue( $processResult->wasSuccess() );
       $this->assertTrue( $processResult->complete );
    }

    public function testFailedResult(){
        $argsProvider = new class extends AbstractArgProvider {
            public function getPage():int{
                return 2;
            }
            
            public function getArgs(): array{
                return [];
            }
        };
        $handler = new class implements RecivesResults {
            public function handle($results): bool
            {
                return false;
            }
        };
        $query = new \WP_Query();
        $query->max_num_pages = 42;
        $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            $query
       );
       $this->assertFalse( $processResult->wasSuccess() );
    }
}