<?php

namespace modules\users\controllers;

use classes\BaseController;
use classes\App;

class PasswdChangeController extends BaseController
{    
    
    public function __construct() {
        parent::__construct();
        $this->title = 'Смена пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $this->user_flag = 'passwd';
    }

    /**
     * @return bool|string
     */
    private function checkInput(array $input)
    {
        if(!password_verify(App::$input['old_passwd'], App::$user->passwd)) {
            return App::$message->get('error', [], 'Вы неверно ввели старый пароль');
        }elseif( strlen($input['new_passwd1'])<8 ){
            return App::$message->get('error', [], 'Новый пароль не может быть короче восьми символов');
        }elseif( !strlen($input['new_passwd2']) ){
            return App::$message->get('error', [], 'Повторите новый пароль');
        }elseif(strcmp($input['new_passwd1'],App::$input['new_passwd2'])!=0){
            return App::$message->get('error', [], 'Пароли не совпадают');            
        } else {
            return true;
        }
    }


    private function passwdChange(): string 
    {
        if(($content = $this->checkInput(App::$input)) === true) {
            App::$user->salt = App::$user->generateSalt();            
            App::$user->passwd = App::$user->encryptPassword(App::$input['new_passwd1'], App::$user->salt);
            App::$user->save();
            $content = App::$message->get('info',[],'Пароль успешно изменен !');
        }
        return $content;
    }


    public function actionIndex(): string
    {
        $this->title = 'Смена пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $content = '';
        if (App::$user->id && App::$input['passwd_change'] && check_csrf_token()) {
            $content = $this->passwdChange();
        }
        
        if (App::$user->id) {
            $content .= App::$template->parse('user_passwd_change');
            return $content;
        } else {
            redirect('login/');
        }        
    }
}

