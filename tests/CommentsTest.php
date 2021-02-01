<?php

use PHPUnit\Framework\TestCase;
use classes\Comments;
use classes\App;
use classes\User;
use classes\Message;
use classes\Template;

/**
 * Return CSRF token value from session.
 *
 * @return string Output string
 */
function get_csrf_token()
{
    global $_SESSION;
    if (!array_key_exists('CSRF_Token', $_SESSION)) {
        $token = App::$user->encryptPassword(App::$user->generateSalt(), App::$user->generateSalt());
        $_SESSION['CSRF_Token'] = $token;
    }
    return $_SESSION['CSRF_Token'];
}

/**
 * Compare CSRF token from session and input.
 *
 * @return bool Output string
 */
function check_csrf_token(): bool
{
    global $_SESSION;
    return App::$input['CSRF_Token'] === $_SESSION['CSRF_Token'];
}


class CommentsTest extends TestCase
{
    public function setUp() : void
    {
        global $_SESSION;

        App::$user = new User;
        App::$template = new Template();
        App::$message = new Message();
        $_SESSION['CSRF_Token'] = '';
    }

    public function testCommentsForm(): void
    {
        $Comments = new Comments('test');
        $content = $Comments->show_form();
        self::assertStringStartsWith('<a name="comment_form"></a>', $content);
    }

    public function testCommentsFormValidationName(): void
    {
        global $_SESSION;
        $Comments = new Comments('test');

        App::$input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1']);
        $content = $Comments->show_form();
        self::assertStringContainsString('Вы неверно ввели свое имя !', $content);
    }

    public function testCommentsFormValidationEMail(): void
    {
        global $_SESSION;
        $Comments = new Comments('test');

        App::$input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1','author'=>'test']);
        $content = $Comments->show_form();
        self::assertStringContainsString('Вы неверно ввели E-Mail адрес !', $content);
    }

    public function testCommentsFormValidationMessage(): void
    {
        global $_SESSION;
        $Comments = new Comments('test');

        App::$input['CSRF_Token'] = $_SESSION['CSRF_Token'];
        $Comments->get_form_data(['add_comment'=>'1','author'=>'test', 'email'=>'test@test.com']);
        $content = $Comments->show_form();
        self::assertStringContainsString('Вы не ввели сообщение, или оно слишком короткое !', $content);
    }
}
