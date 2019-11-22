<?php
if(!isset($input)) {
    require '../../include/common.php';
}

use Classes\App;
use Classes\User;

$tags['Header'] = 'Восстановление пароля';
add_nav_item($tags['Header']);

if ($input['token'] && check_csrf_token()) {
    if($user = App::$user->checkToken($input['token'])) {    
        if ($input['passwd_change']) {    
            if( strlen($input['new_passwd1'])<8 ){
                $content .= my_msg_to_str('error','','Новый пароль не может быть короче восьми символов');
            }elseif( !strlen($input['new_passwd2']) ){
                $content .= my_msg_to_str('error','','Повторите новый пароль');
            }elseif(strcmp($input['new_passwd1'],$input['new_passwd2'])!=0){
                $content .= my_msg_to_str('error','','Пароли не совпадают');            
            }else{
                unset($data);
                $data['salt']=$user['salt'];
                if (mb_strlen($data['salt']) !== 22) {
                    $data['salt']=App::$user->generateSalt();
                }
                $data['passwd']=App::$user->encryptPassword($input['new_passwd1'], $data['salt']);
                $query="update users set ". db_update_fields($data) ." where id='".$user['id']."'";
                $result = App::$db->query($query, true);
                App::$user->makeToken($user['id'], 0, User::TOKEN_NULL);
                $content .= my_msg_to_str('info','','Пароль успешно изменен !');   
                echo get_tpl_default($tags, '', $content);
                exit;
            }
        }
        $tags['token'] = $input['token'];
        $content.=get_tpl_by_name('user_passwd_recovery_confirm', $tags);        
        echo get_tpl_default($tags, '', $content);
        exit;
    } else {
        $content .= my_msg_to_str('error','','Неверный код.');
    }
}

if ($input['passwd_recovery'] && check_csrf_token()) {    
    $query = "select id from users where email='".$input['email']."'";
    $result = App::$db->query($query);
    if ($result->num_rows) {
        list($user_id) = $result->fetch_array();
        $token = App::$user->makeToken($user_id, 1, User::TOKEN_SALT);
        $URL = App::$server['REQUEST_SCHEME'] . '://' . App::$server['HTTP_HOST'] . App::$server['REQUEST_URI'] . '?token=' . $token;
        $message = 'Перейдите по ссылке ' . $URL . ' чтобы задать новый пароль';
        send_mail($input['email'], 'Восстановление пароля на сайте ' . App::$server['HTTP_HOST'], $message);
        $content .= my_msg_to_str('info','','Письмо с инструкцией отправлено.');

    } else {
        $content .= my_msg_to_str('notice','','Такая почта не зарегистрирована');
    }
}

$content.=get_tpl_by_name('user_passwd_recovery', $tags);
echo get_tpl_default($tags, '', $content);