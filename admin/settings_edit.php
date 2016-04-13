<?php

$tags['Header'] = 'Настройки';
include '../include/common.php';

if ($input['del']) {
    $query = "delete from settings where id='{$input['id']}'";
    my_query($query);
}

if ($input['add']) {
    $query = "insert into settings " . db_insert_fields($input['form']);
    my_query($query);
}

if ($input['edit']) {
    $query = "update settings set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input['view']) || ($input['adding'])) {
    if ($input['view']) {
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

