<?php

use PHPUnit\Framework\TestCase;

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
        $handle = fopen($filePath, "r");
        $count = PluginNamespace\Helpers\getCsvSize($filePath);
        $lineNumber = 0;
        $rows = [];
        while (($raw_string = fgets($handle)) !== false) {
            if( $lineNumber >= $start  ){
                    $rows[] = str_getcsv($raw_string);
            }
            
         
            $lineNumber++;
            if( $lineNumber > $end ){
                break;
            }
        }

        fclose($handle);
        $this->assertSame('two',$rows[0][0]);
        $this->assertSame('three',$rows[1][0]);
        
    }

}