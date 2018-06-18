<?php

use PHPUnit\Framework\TestCase;
use Classes\MyGlobal;

require_once 'tests/require.php';

class MyGlobalTest extends TestCase
{

    public function testMyGlobal()
    {
        MyGlobal::set('test', '123' );
        self::assertEquals('123', MyGlobal::get('test'));
    }
    
}

