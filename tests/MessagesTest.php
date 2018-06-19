<?php

use PHPUnit\Framework\TestCase;

require_once 'tests/bootstrap.php';

class MessagesTest extends TestCase
{

    public function test_my_msg_to_str()
    {
        $msg=my_msg_to_str('debug',[],'test');
        self::assertEquals($msg, '<p class="alert normal-form alert-success">test</p>');
    }
    
}

