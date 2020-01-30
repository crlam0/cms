<?php

namespace modules\misc;

use Classes\BaseController;
use Classes\App;

class PasswdChangeController extends BaseController
{    
    
    private function checkInput($input, $row) {
        if(strcmp(App::$user->encryptPassword(App::$input['old_passwd'], $row['salt']),$row['passwd'])!=0){
            return App::$message->get('error','','Вы неверно ввели старый пароль');
        }elseif( strlen($input['new_passwd1'])<8 ){
            return App::$message->get('error','','Новый пароль не может быть короче восьми символов');
        }elseif( !strlen($input['new_passwd2']) ){
            return App::$message->get('error','','Повторите новый пароль');
        }elseif(strcmp($input['new_passwd1'],App::$input['new_passwd2'])!=0){
            return App::$message->get('error','','Пароли не совпадают');            
        } else {
            return true;
        }
    }


    private function passwdChange() {
        $query = "select passwd,salt from users where id='".App::$user->id."'";
        $result = App::$db->query($query, true);
        $content = '';
        if (!$result->num_rows) {
            return false;
        }
        $row = $result->fetch_array();
        if(($content = $this->checkInput(App::$input, $row)) === true) {
            $data=[];
            $data['salt']=$row['salt'];
            if (mb_strlen($data['salt']) !== 22) {
                $data['salt']=App::$user->generateSalt();
            }
            $data['passwd']=App::$user->encryptPassword(App::$input['new_passwd1'], $data['salt']);
            $query="update users set ". db_update_fields($data) ." where id='".App::$user->id."'";
            App::$db->query($query);
            $content = App::$message->get('info','','Пароль успешно изменен !');            
        }
        return $content;
    }


    public function actionIndex()
    {
        $this->title = 'Смена пароля';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $content = '';
        if (App::$user->id && App::$input['passwd_change'] && check_csrf_token()) {
            $content = $this->passwdChange();
        }
        
        if (App::$user->id) {
            $content .= App::$template->parse('user_passwd_change', $tags);
            return $content;
        } else {
            redirect('login/');
        }        
    }
}

