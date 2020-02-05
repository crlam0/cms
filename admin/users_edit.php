<?php
$tags['Header']="Пользователи сервера";
include "../include/common.php";

use Classes\App;

if ($input['del']) {
    $query = "delete from users where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str("", "", "Пользователь успешно удален !");
}

if ($input['add']) {
    $query="select id from users where login='".$input['form']['login']."'";
    $result=my_query($query);
    if($result->num_rows){
            $content.=my_msg_to_str('error','','Такой пользователь уже существует ! ('.$input['form']['login'].')');
    }else{
        $input['form']['flags'] = implode(";", $input['flags']);
        $input['form']['salt'] = App::$user->generateSalt();
        $input['form']['passwd'] = App::$user->encryptPassword($input['form']['passwd'], $input['form']['salt']);
        $input['form']['regdate'] = 'now()';
        $query = "insert into users " . db_insert_fields($input['form']);
        my_query($query);
        $content.=my_msg_to_str('', '', 'Пользователь успешно добавлен !');
    }
}

if ($input['edit']) {
    $input['form']['flags']=implode(";", $input['flags']);
    if(strlen($input['form']['passwd'])){
        $input['form']['salt'] = App::$user->generateSalt();
        $input['form']['passwd']= App::$user->encryptPassword($input['form']['passwd'] , $input['form']['salt'] );
    }else{
        unset($input['form']['passwd']);
    }
    $query = "update users set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', '', 'Редактирование успешно завершено !');
}

if (($input['view']) || ($input['adding'])) {
    if ($input['view']) {
        $query = "select * from users where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $flags=@explode(";",$tags[flags]);
        $tags['type'] = 'edit';
        $tags['form_title'] = 'Редактирование';
    } else {
        $flags=@explode(";","active;passwd;");
        $tags['type'] = 'add';
        $tags['form_title'] = 'Добавление';
    }
    $tags['flags']="";
    $query="select * from users_flags order by title asc";
    $result_flags=my_query($query, true);
    while($row_flags=$result_flags->fetch_array()){
            $tags['flags'].="<input type=checkbox name=flags[] ".(in_array($row_flags['value'], $flags) ? "checked" : "")." value=\"{$row_flags['value']}\">{$row_flags['title']}<br>";
    }

    $content.=get_tpl_by_name('users_edit_form', $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
} else {
    $query = "SELECT * from users order by login asc";
    $result = my_query($query);
    $content.=get_tpl_by_name('users_edit_table', $tags, $result);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
}

