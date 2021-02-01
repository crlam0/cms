<?php

use PHPUnit\Framework\TestCase;
use classes\Message;

class MessagesTest extends TestCase
{

    public function testMessageGet(): void
    {
        $Message = new Message;
        $msg = $Message->get('debug', [], 'test');
        self::assertEquals('<p class="alert normal-form alert-info">test</p>', $msg);
    }
}
