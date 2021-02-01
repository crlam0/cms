<?php

namespace modules\users\controllers;

use classes\BaseController;
use classes\App;
use classes\User;

class SignupController extends BaseController
{


    /**
     * @return bool|string
     */
    private function checkInput($form)
    {
        if (strlen($form['login'])<4) {
            return App::$message->get('error', [], 'Логин не может быть короче четырёх символов');
        }
        if (strlen($form['email'])<8) {
            return App::$message->get('error', [], 'Новый пароль не может быть короче восьми символов');
        }
        if (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $form['email'])) {
            return App::$message->get('error', [], 'Неверный адрес E-Mail');
        }
        if (App::$db->getRow("select login from users where login = '{$form['login']}'") !== false) {
            return App::$message->get('error', [], 'Пользователь ' . $form['login'] . ' уже зарегистрирован');
        }
        if (App::$db->getRow("select email from users where email = '{$form['email']}'") !== false) {
            return App::$message->get('error', [], 'E-Mail ' . $form['email'] . ' уже зарегистрирован');
        }
        if (strlen($form['new_passwd1'])<8) {
            return App::$message->get('error', [], 'Новый пароль не может быть короче восьми символов');
        }
        if (!strlen($form['new_passwd2'])) {
            return App::$message->get('error', [], 'Повторите новый пароль');
        }
        if (strcmp($form['new_passwd1'], $form['new_passwd2'])!=0) {
            return App::$message->get('error', [], 'Пароли не совпадают');
        }
        return true;
    }

    public function actionStep2(): string
    {
        global $_SESSION;
        $this->title = 'Регистрация';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $user = App::$user->checkToken(App::$input['token']);
        if (!$user) {
            return App::$message->get('error', [], 'Неверный код.');
        }
        $data['flags'] = implode(";", ['active', 'passwd']);
        App::$db->updateTable('users', $data, ['id' => $user['id']]);
        App::$user->makeToken($user['id'], 0, User::TOKEN_NULL);
        App::$user->authByIdFlags($user['id'], $data['flags']);
        $_SESSION['UID'] = $user['id'];
        $_SESSION['FLAGS'] = $data['flags'];
        return App::$message->get('info', [], 'Учётная запись активирована');
        ;
    }

    public function actionStep1(): string
    {
        $this->title = 'Регистрация';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $content = '';
        $form = App::$input['form'];
        if (App::$input['signup'] && ($content = $this->checkInput($form)) === true && check_csrf_token()) {
            $form['flags'] = implode(";", ['new_user']);
            $form['salt'] = App::$user->generateSalt();
            $form['passwd'] = App::$user->encryptPassword($form['new_passwd1'], $form['salt']);
            $form['regdate'] = 'now()';
            unset($form['new_passwd1']);
            unset($form['new_passwd2']);
            // $query = "insert into users " . db_insert_fields($form);
            // App::$db->query($query);
            App::$db->insertTable('users', $form);
            $user_id = App::$db->insert_id();
            $token = App::$user->makeToken($user_id, 1, User::TOKEN_SALT);
            $URL = App::$server['REQUEST_SCHEME'] . '://' . App::$server['HTTP_HOST'] . App::$SUBDIR . 'signup/step2?token=' . $token;
            $message = 'Перейдите по ссылке ' . $URL . ' чтобы активировать учётную запись.';
            if (!App::$debug) {
                App::$message->mail(App::$input['form']['email'], 'Регистрация на сайте ' . App::$server['HTTP_HOST'], $message);
            } else {
                echo $message;
            }
            return App::$message->get('info', [], 'Письмо с инструкцией отправлено.');
        }
        return $content . App::$template->parse('user_signup.html.twig', $form);
    }

    public function actionIndex(): string
    {
        $this->title = 'Регистрация';
        $this->breadcrumbs[] = ['title'=>$this->title];

        if (!App::$user->id) {
            $tags = [
                'login' => '',
                'email' => '',
                'fullname' => '',
            ];
            return App::$template->parse('user_signup.html.twig', $tags);
        } else {
            return App::$message->get('user_already_logged_on');
        }
    }
}
