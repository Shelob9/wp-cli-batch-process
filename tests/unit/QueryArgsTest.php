<?php

use PHPUnit\Framework\TestCase;
use WpCliBatchProcess\AbstractArgProvider;

class QueryArgsTestCase extends TestCase {

	public function testGetArgs()
	{
		$argsProvider = new class extends AbstractArgProvider {
			public function getArgs(): array{
				return[];
			}
            public function getPage():int{
                return 2;
            }
      
            public function getPerPage():int
			{
				return 5;
			}
        };
		$this->assertEquals(
			[
				'post_status' =>'draft',
				'paged' => 2,
				'posts_per_page' => 5
			],
			$argsProvider->mergeArgs([
				'paged' =>5500,
				'post_status' =>'draft'
			])
		);
	}
}