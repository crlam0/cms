<?php

use PHPUnit\Framework\TestCase;
use Classes\Comments;
use Classes\App;
use Classes\User;

require_once 'tests/bootstrap.php';
require_once 'include/lib_templates.php';
require_once 'include/lib_functions.php';
require_once 'include/lib_messages.php';

class CommentsTest extends TestCase
{

    public function setUp()
    {
        App::$user = new User;
    }
    public function testCommentsForm()
    {
        $Comments = new Comments('test');
        $content = $Comments->show_form();
        self::assertStringStartsWith('<a name="comment_form"></a>', $content);
    }

    public function testCommentsFormValidationName()
    {
        $Comments = new Comments('test');

        $input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1']);
        $content = $Comments->show_form();
        self::assertContains('Вы неверно ввели свое имя !', $content);

    }

    public function testCommentsFormValidationEMail()
    {
        $Comments = new Comments('test');

        $input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1','form'=>['author'=>'test']]);
        $content = $Comments->show_form();
        self::assertContains('Вы неверно ввели E-Mail адрес !', $content);
    }

    public function testCommentsFormValidationMessage()
    {
        $Comments = new Comments('test');

        $input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1','form'=>['author'=>'test', 'email'=>'test@test.com']]);
        $content = $Comments->show_form();
        self::assertContains('Вы не ввели сообщение, или оно слишком короткое !', $content);
    }

    
}

