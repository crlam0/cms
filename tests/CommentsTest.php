<?php

use PHPUnit\Framework\TestCase;
use Classes\Comments;

require_once 'tests/bootstrap.php';

class CommentsTest extends TestCase
{

    public function testCommentsForm()
    {
        $Comments = new Comments('test');
        $content = $Comments->show_form();
        self::assertStringStartsWith('<a name="comment_form"></a>', $content);
    }

    public function testCommentsFormValidation()
    {
        $Comments = new Comments('test');

        $Comments->get_form_data(['add_comment'=>'1']);
        $content = $Comments->show_form();
        self::assertContains('Вы неверно ввели свое имя !', $content);

        $Comments->get_form_data(['add_comment'=>'1','form'=>['author'=>'test']]);
        $content = $Comments->show_form();
        self::assertContains('Вы неверно ввели E-Mail адрес !', $content);

        $Comments->get_form_data(['add_comment'=>'1','form'=>['author'=>'test', 'email'=>'test@test.com']]);
        $content = $Comments->show_form();
        self::assertContains('Вы не ввели сообщение, или оно слишком короткое !', $content);
    }

    
}

