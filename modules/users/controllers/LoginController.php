<?php

namespace modules\users\controllers;

use classes\BaseController;
use classes\App;

class LoginController extends BaseController
{

    private function auth(): string
    {
        global $COOKIE_NAME;
        if ($row = App::$user->authByLoginPassword(App::$input['login'], App::$input['passwd'])) {
            App::$session['UID'] = App::$user->id;
            App::$session['FLAGS'] = App::$user->flags;
            if (App::$input['rememberme']) {
                App::$user->setRememberme($COOKIE_NAME);
            }
            if (mb_strlen($row['salt']) !== 22) {
                return App::$message->get('notice', [], 'Ваш пароль устарел. Пожалуйста, поменяйте его на другой <a href="' . App::$SUBDIR . 'passwd-change/" />по этой ссылке</a> ');
            }
            if (isset(App::$session['GO_TO_URI'])) {
                $uri = App::$session['GO_TO_URI'];
                unset(App::$session['GO_TO_URI']);
                redirect($uri);
            } else {
                redirect(App::$SUBDIR);
            }
        }
        return App::$message->get('user_login_failed');
    }


    public function actionIndex(): string
    {
        $this->title = 'Вход в систему';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $content = '';
        $tags = [];
        if (App::$input['logon'] && check_csrf_token()) {
            $content = $this->auth();
        }

        if (!App::$user->id) {
            if (isset(App::$input['login'])) {
                $tags['login'] = App::$input['login'];
            }
            $content .= App::$template->parse('user_login_promt.html.twig', $tags);
        } else {
            $content .= App::$message->get('user_already_logged_on');
        }
        return $content;
    }
}
