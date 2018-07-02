<?php

$tags['Header'] = 'Настройки';
include '../include/common.php';

$content = '';

if (check_key('del',$input)) {
    $query = "delete from settings where id='{$input['id']}'";
    my_query($query);
}

if (check_key('add',$input)) {
    $query = "insert into settings " . db_insert_fields($input['form']);
    my_query($query);
}

if (check_key('edit',$input)) {
    $query = "update settings set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if ( (check_key('view',$input)) || (check_key('adding',$input)) ) {
    if (check_key('view',$input)) {
        $query = "select * from settings where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = 'edit';
        $tags['form_title'] = 'Редактирование';
    } else {
        $tags['type'] = 'add';
        $tags['form_title'] = 'Добавление';
    }
    $content.=get_tpl_by_title('settings_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
} else {
    $query = "SELECT * from settings order by title asc";
    $result = my_query($query);
    $content.=get_tpl_by_title('settings_edit_table', $tags, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
}

