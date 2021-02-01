<?php

namespace modules\users\controllers;

use classes\BaseController;
use classes\App;
use modules\users\models\User;

class PasswdRecoveryController extends BaseController
{


    /**
     * @return bool|string
     */
    private function checkInput()
    {
        if (strlen(App::$input['new_passwd1'])<8) {
            return App::$message->get('error', [], 'Новый пароль не может быть короче восьми символов');
        } elseif (!strlen(App::$input['new_passwd2'])) {
            return App::$message->get('error', [], 'Повторите новый пароль');
        } elseif (strcmp(App::$input['new_passwd1'], App::$input['new_passwd2'])!=0) {
            return App::$message->get('error', [], 'Пароли не совпадают');
        } else {
            return true;
        }
    }

    private function passwdChange(User $user): string
    {
        $user->salt = $user->generateSalt();
        $user->passwd = $user->encryptPassword(App::$input['new_passwd1'], $user->salt);
        $user->save();
        $user->makeToken(0, User::TOKEN_NULL);
        return App::$message->get('info', [], 'Пароль успешно изменен !');
    }

    public function actionStep2(): string
    {
        $this->title = 'Восстановление пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $content = '';
        $user = new User();
        $user->findByToken(App::$input['token']);
        if (!$user->id) {
            return App::$message->get('error', [], 'Неверный код.');
        }
        if (App::$input['passwd_change'] && ($content = $this->checkInput()) === true && check_csrf_token()) {
            return $this->passwdChange($user);
        }
        $tags['token'] = App::$input['token'];
        $content .= App::$template->parse('user_passwd_recovery_confirm.html.twig', $tags);
        return $content;
    }

    public function actionStep1(): string
    {
        $this->title = 'Восстановление пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];

        if (App::$input['passwd_recovery'] && check_csrf_token()) {
            $user = new User();
            $user->findByEmail(App::$input['email']);
            if ($user->id) {
                $token = $user->makeToken(1, User::TOKEN_SALT);
                $URL = App::$server['REQUEST_SCHEME'] . '://' . App::$server['HTTP_HOST'] . App::$SUBDIR . 'passwd_recovery/step2?token=' . $token;
                $message = 'Перейдите по ссылке ' . $URL . ' чтобы задать новый пароль';
                App::$message->mail(App::$input['email'], 'Восстановление пароля на сайте ' . App::$server['HTTP_HOST'], $message);
                return App::$message->get('info', [], 'Письмо с инструкцией отправлено.');
            } else {
                return App::$message->get('notice', [], 'Такая почта не зарегистрирована');
            }
        }
    }

    public function actionIndex(): string
    {
        $this->title = 'Восстановление пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];

        if (!App::$user->id) {
            return App::$template->parse('user_passwd_recovery.html.twig', []);
        } else {
            return App::$message->get('user_already_logged_on');
        }
    }
}
