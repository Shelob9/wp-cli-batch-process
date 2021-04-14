<?php

use PHPUnit\Framework\TestCase;
use PluginNamespace\Something;
class SomethingTest extends TestCase {
    public function testSomeThing(){
        $this->assertTrue(Something::hiRoy());
    }
}