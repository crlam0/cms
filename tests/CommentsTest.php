<?php

use PHPUnit\Framework\TestCase;
use Classes\Comments;

require_once 'tests/require.php';

class CommentsTest extends TestCase
{

    public function testComments()
    {
        $Comments = new Comments('test');
        $content = $Comments->show_form();
        self::assertStringStartsWith('<a name="comment_form"></a>', $content);
    }
    
}

