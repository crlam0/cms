<?php

use PHPUnit\Framework\TestCase;
use Classes\Comments;
use Classes\App;
use Classes\User;
use Classes\Message;
use Classes\Template;

/**
 * Return CSRF token value from session. 
 *
 * @return string Output string
 */
function get_csrf_token() {
    global $_SESSION;
    if(!array_key_exists('CSRF_Token',$_SESSION)) {
        $token = App::$user->encryptPassword(App::$user->generateSalt(), App::$user->generateSalt());
        $_SESSION['CSRF_Token'] = $token;
    }
    return $_SESSION['CSRF_Token'];
}

/**
 * Compare CSRF token from session and input. 
 *
 * @return string Output string
 */
function check_csrf_token() {
    global $_SESSION;
    return App::$input['CSRF_Token'] === $_SESSION['CSRF_Token'];
}


class CommentsTest extends TestCase
{
    public function setUp()
    {
        App::$user = new User;
        App::$template = new Template();
        App::$message = new Message();
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
        $Comments->get_form_data(['add_comment'=>'1','author'=>'test']);
        $content = $Comments->show_form();
        self::assertContains('Вы неверно ввели E-Mail адрес !', $content);
    }

    public function testCommentsFormValidationMessage()
    {
        $Comments = new Comments('test');

        $input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1','author'=>'test', 'email'=>'test@test.com']);
        $content = $Comments->show_form();
        self::assertContains('Вы не ввели сообщение, или оно слишком короткое !', $content);
    }

    
}

