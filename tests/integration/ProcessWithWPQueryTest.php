<?php

use PHPUnit\Framework\TestCase;
use WpCliBatchProcess\AbstractArgProvider;
use WpCliBatchProcess\DeleteHandler;
use WpCliBatchProcess\Helpers;
use WpCliBatchProcess\ProvidesQueryArgs;
use WpCliBatchProcess\RecivesResults;
use WpCliBatchProcess\ProcessResult;
class ProcessWithWpQuery extends \WP_UnitTestCase {

    public function testProcessor(){
        self::factory()->post->create_many(50,[
            'post_type' => 'post'
        ]);
       
        $argsProvider = new class extends AbstractArgProvider {
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
       $this->assertTrue( $processResult->wasSuccess() );
       $this->assertFalse($processResult->complete);
       $argsProvider->setPage(2);
       $query = new \WP_Query();
       $processResult = WpCliBatchProcess\Helpers\processWithWpQuery(
            $argsProvider,  
            $handler,
            $query
        );
       
       $this->assertTrue( $processResult->wasSuccess() );
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

	public function testQueryRun()
	{
		self::factory()->post->create_many(50,[
            'post_type' => 'page',
			'post_status' => 'publish'
        ]);
		self::factory()->post->create_many(50,[
            'post_type' => 'page',
			'post_status' => 'draft'
        ]);
		$processor = [
        	'type' => 'WP_Query',
			'source' => __DIR__ .'/delete-pages.json',
			'handler' => 'WpCliBatchProcess::DeleteHandler'
		];
		\WpCliBatchProcess\Helpers\processRun(
			1,25,$processor
		);
		//Were 25 published pages deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'page',
			 'paged' => 1,
			 'posts_per_page' => 100,
			 'post_status' => 'publish'
		]);
		$testQuery->get_posts();
		$this->assertSame(25, $testQuery->post_count);

		//Were NO draft posts deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'page',
			 'paged' => 1,
			 'posts_per_page' => 100,
			 'post_status' => 'draft'
		]);
		$testQuery->get_posts();
		$this->assertSame(50, $testQuery->post_count);

		\WpCliBatchProcess\Helpers\processRun(
			1,15,$processor
		);
		//Were 15 more published pages deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'page',
			 'paged' => 1,
			 'posts_per_page' => 100,
			 'post_status' => 'publish'
		]);
		
		$testQuery->get_posts();
		$this->assertSame(10, $testQuery->post_count);
	}

	public function testProcessAndDelete(){
		self::factory()->post->create_many(75,[
            'post_type' => 'post'
        ]);
       
        $argsProvider = new class extends AbstractArgProvider {
			
            public function getArgs(): array{
                return $this->mergeArgs([
                    'post_type' => 'post',
                ]);
            }
        };
        $handler = new DeleteHandler();
		$result = \WpCliBatchProcess\Helpers\processWithWpQueryAndDelete(
			$argsProvider,$handler, new \WP_Query()
		);
		$this->assertTrue($result->wasSuccess());
		$this->assertFalse($result->complete);	
		
		//Were 25 posts deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'post',
			 'posts_per_page' => 100,
		]);
		
		$testQuery->get_posts();
		$this->assertSame(50, $testQuery->post_count);

		//process page 2
		$argsProvider->setPage(2);
		$result = \WpCliBatchProcess\Helpers\processWithWpQueryAndDelete(
			$argsProvider,$handler, new \WP_Query()
		);
		$this->assertTrue($result->wasSuccess(),json_encode($result));
		$this->assertFalse($result->complete,json_encode($result));	

		//Were 25 more posts deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'post',
			 'posts_per_page' => 100,
		]);
		
		$testQuery->get_posts();
		$this->assertSame(25, $testQuery->post_count);

		//process page 3
		$argsProvider->setPage(3);
		$this->assertSame(3,$argsProvider->getPage());
		$result = \WpCliBatchProcess\Helpers\processWithWpQueryAndDelete(
			$argsProvider,$handler, new \WP_Query()
		);

		//Were 25 more posts deleted?
		$testQuery = new WP_Query([
			 'post_type' => 'post',
			 'posts_per_page' => 100,
		]);
		
		$testQuery->get_posts();
		$this->assertSame(0, $testQuery->post_count,json_encode($result));

		$this->assertTrue($result->wasSuccess());
		$this->assertTrue($result->complete,
	json_encode($result));	
	}
    
}