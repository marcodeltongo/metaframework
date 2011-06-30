<?php

class CommonTest extends CDbTestCase
{
    public $fixtures = array(
    );

    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function testFalse()
    {
        $this->assertTrue(false);
    }
}