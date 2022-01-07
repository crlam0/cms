<?php

use classes\Message;

class MessagesTest extends \Codeception\Test\Unit
{

    public function testMessageGet(): void
    {
        $Message = new Message;
        $msg = $Message->get('debug', [], 'test');
        self::assertEquals('<p class="alert normal-form alert-info">test</p>', $msg);
    }
}
