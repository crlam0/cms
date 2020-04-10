<?php

namespace modules\misc;

use Classes\BaseController;
use Classes\App;
use Classes\User;

class PasswdRecoveryController extends BaseController
{    
    
    private function checkInput()
    {
        if( strlen(App::$input['new_passwd1'])<8 ){
            return App::$message->get('error', [], 'Новый пароль не может быть короче восьми символов');
        }elseif( !strlen(App::$input['new_passwd2']) ){
            return App::$message->get('error', [], 'Повторите новый пароль');
        }elseif(strcmp(App::$input['new_passwd1'],App::$input['new_passwd2'])!=0){
            return App::$message->get('error', [], 'Пароли не совпадают');            
        } else {
            return true;
        }
    }
    
    private function passwdChange(array $user): string 
    {
        $data = [];
        $data['salt']=$user['salt'];
        if (mb_strlen($data['salt']) !== 22) {
            $data['salt']=App::$user->generateSalt();
        }
        $data['passwd']=App::$user->encryptPassword(App::$input['new_passwd1'], $data['salt']);
        $query="update users set ". db_update_fields($data) ." where id='".$user['id']."'";
        App::$db->query($query);
        App::$user->makeToken($user['id'], 0, User::TOKEN_NULL);
        return App::$message->get('info',[],'Пароль успешно изменен !');          
    }

    public function actionStep2(): string 
    {
        $this->title = 'Восстановление пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $content = '';        
        $user = App::$user->checkToken(App::$input['token']);
        if(!$user) {
            return App::$message->get('error',[],'Неверный код.');
        }
        if(App::$input['passwd_change'] && ($content = $this->checkInput()) === true && check_csrf_token()) {
            return $this->passwdChange($user);
        }
        $tags['token'] = App::$input['token'];
        $content .= App::$template->parse('user_passwd_recovery_confirm', $tags);
        return $content;
    }

    public function actionStep1(): string 
    {
        $this->title = 'Восстановление пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];
        
        if (App::$input['passwd_recovery'] && check_csrf_token()) {    
            $query = "select id from users where email='".App::$input['email']."'";
            $result = App::$db->query($query);
            if ($result->num_rows) {
                list($user_id) = $result->fetch_array();
                $token = App::$user->makeToken($user_id, 1, User::TOKEN_SALT);
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
            return App::$template->parse('user_passwd_recovery', []);
        } else {
            return App::$message->get('user_already_logged_on');
        }        
    }
}

