<?php

namespace modules\misc;

use Classes\BaseController;
use Classes\App;

class LoginController extends BaseController
{    
    private function auth(): string 
    {
        global $_SESSION, $COOKIE_NAME;
        if($row = App::$user->authByLoginPassword(App::$input['login'],App::$input['passwd'])) {
            $_SESSION['UID']=App::$user->id;
            $_SESSION['FLAGS']=App::$user->flags;
            if(App::$input['rememberme']) {
                App::$user->setRememberme(App::$user->id,$COOKIE_NAME);
            }            
            if (mb_strlen($row['salt']) !== 22) {
                return App::$message->get('notice','','Ваш пароль устарел. Пожалуйста, поменяйте его на другой <a href="'.App::$SUBDIR.'passwd_change/" />по этой ссылке</a> ');
            }
            if (strlen($_SESSION['GO_TO_URI'])) {
                $uri=$_SESSION['GO_TO_URI'];
                unset($_SESSION['GO_TO_URI']);
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
            if(isset(App::$input['login'])) {
                $tags['login'] = App::$input['login'];
            }
            $content .= App::$template->parse('user_login_promt', $tags);
            return $content;
        } else {
            return App::$message->get('user_already_logged_on');
        }        
    }
}

