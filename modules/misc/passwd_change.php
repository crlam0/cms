<?php
if(!isset($input)) {
    require '../../include/common.php';
}

use Classes\App;

$tags['Header'] = 'Смена пароля';
$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";
add_nav_item($tags['Header']);

if ($input['passwd_change']) {    
    $query = "select passwd,salt from users where id='".App::$user->id."'";
    $result = my_query($query, true);
    if ($result->num_rows) {
        $row = $result->fetch_array();   
        if(strcmp(App::$user->encryptPassword($input['old_passwd'], $row['salt']),$row['passwd'])!=0){
            $content .= my_msg_to_str('error','','Вы неверно ввели старый пароль');
        }elseif( strlen($input['new_passwd1'])<8 ){
            $content .= my_msg_to_str('error','','Новый пароль не может быть короче восьми символов');
        }elseif( !strlen($input['new_passwd2']) ){
            $content .= my_msg_to_str('error','','Повторите новый пароль');
        }elseif(strcmp($input['new_passwd1'],$input['new_passwd2'])!=0){
            $content .= my_msg_to_str('error','','Пароли не совпадают');            
        }else{
            unset($data);
            $data['salt']=$row['salt'];
            if (mb_strlen($data['salt']) !== 22) {
                $data['salt']=App::$user->generateSalt();
            }
            $data['passwd']=App::$user->encryptPassword($input['new_passwd1'], $data['salt']);
            $query="update users set ". db_update_fields($data) ." where id='{App::$user->id}'";
            $result = my_query($query, true);
            $content .= my_msg_to_str('info','','Пароль успешно изменен !');            
        }
    }
}

$content.=get_tpl_by_name('user_passwd_change', $tags);
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);