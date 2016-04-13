<?php

$tags['Header'] = 'Смена пароля';
include '../include/common.php';
$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";

if ($input['passwd_change']) {    
    $query = "select passwd,salt from users where id='".$_SESSION['UID']."'";
    $result = my_query($query, $conn, true);
    if ($result->num_rows) {
        $row = $result->fetch_array();   
        if(strcmp(user_encrypt_password($input['old_passwd'], $row['salt']),$row['passwd'])!=0){
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
                $data['salt']=user_generate_salt();
            }
            $data['passwd']=user_encrypt_password($input['new_passwd1'], $data['salt']);
            $query="update users set ". db_update_fields($data) ." where id='{$_SESSION['UID']}'";
            $result = my_query($query, $conn, true);
            $content .= my_msg_to_str('info','','Пароль успешно изменен !');            
        }
    }
}

$content.=get_tpl_by_title('user_passwd_change', $tags);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);