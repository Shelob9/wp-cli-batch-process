<?php

use PHPUnit\Framework\TestCase;
use PluginNamespace\RecivesResults;

class FromCsvTest extends TestCase {


    public function testCSVMax()
    {
        $count = PluginNamespace\Helpers\getCsvSize(__DIR__.'/test-2.csv');
        $this->assertSame(2,$count);
        $count = PluginNamespace\Helpers\getCsvSize(__DIR__.'/test-42.csv');
        $this->assertSame(42,$count);
    }

    public function testGetRows(){
        $filePath = __DIR__.'/test-42.csv';
        $start = 2;
        $end = 4;
        $rows = PluginNamespace\Helpers\getRowsFromCsv($filePath,$start,$end);
        $this->assertSame('two',$rows[0][0]);
        $this->assertSame('three',$rows[1][0]);
        $this->assertCount(2,$rows);

        $start = 10;
        $end = 22;
        $rows = PluginNamespace\Helpers\getRowsFromCsv($filePath,$start,$end);
        $this->assertCount(12,$rows);
        $this->assertSame('ten',$rows[0][0]);
    
    }

    public function testProcessCsvSuccesful(){
        $filePath = __DIR__.'/test-42.csv';
        $start = 2;
        $end = 4;
        $handler = new class implements RecivesResults {
            public function handle($results): bool
            {
                return true;
            }
        };
        $result = PluginNamespace\Helpers\processFromCsv($filePath,$start,$end,$handler);
        $this->assertTrue($result->success);
        $this->assertFalse($result->complete);
    }

    public function testProcessCsvComplete(){
        $filePath = __DIR__.'/test-42.csv';
        $start = 40;
        $end = 42;
        $handler = new class implements RecivesResults {
            public function handle($results): bool
            {
                return true;
            }
        };
        $result = PluginNamespace\Helpers\processFromCsv($filePath,$start,$end,$handler);
        $this->assertTrue($result->success);
        $this->assertTrue($result->complete);
    }
}