<?php

use PHPUnit\Framework\TestCase;
use WpCliBatchProcess\Helpers;
use WpCliBatchProcess\ProvidesQueryArgs;
use WpCliBatchProcess\RecivesResults;
use WpCliBatchProcess\ProcessResult;
class ProcessWithWpQuery extends \WP_UnitTestCase {

    
    public function testProcessor(){
        
        self::factory()->post->create_many(50,[
            'post_type' => 'post'
        ]);
       
        $argsProvider = new class implements ProvidesQueryArgs {
            public $page;
            public function getPage():int{
                return isset($this->page) ? $this->page : 1;
            }
            public function setPage(int $page): int{
                $this->page = $page;
                return $this->page;;
            }
            public function getArgs(): array{
                return [
                    'post_type' => 'post',
                    'posts_per_page' => 25
                ];
            }
        };
        $handler = new class implements RecivesResults {
            public function handle($results): bool
            {
                return true;
            }
        };
        $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            new \WP_Query()
       );
       $this->assertTrue( $processResult->success );
       $this->assertFalse($processResult->complete);
       $argsProvider->setPage(2);
       $query = new \WP_Query();
       $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            $query
        );
       
       $this->assertTrue( $processResult->success );
       $this->assertTrue($processResult->complete);
    }

	public function testDeleteHandler(){
		self::factory()->post->create_many(50,[
            'post_type' => 'post'
        ]);
		
		$query = new WP_Query([
			 'post_type' => 'post',
			 'per_page' => 10
		]);
		$handler = new WpCliBatchProcess\DeleteHandler();
		$this->assertTrue($handler->handle($query->get_posts()));
		$testQuery = new WP_Query([
			 'post_type' => 'post',
			 'paged' => 1,
			 'posts_per_page' => 100
		]);
		$testQuery->get_posts();
		$this->assertSame(40, $testQuery->post_count);
	}
    
}