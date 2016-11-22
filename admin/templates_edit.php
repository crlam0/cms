<?php

$tags['Header'] = 'Шаблоны';
include '../include/common.php';

if ($input['del']) {
    $query = "delete from templates where id='{$input['id']}'";
    my_query($query, null, true);
}

if ($input['add']) {
    $input['content'] = str_replace('text_area', 'textarea', $input['content']);
    $query = "insert into templates " . db_insert_fields($input['form']);
    my_query($query, null, true);
}

if($input['revert']){
    unset($input['edit']);
    $input['view']=1;
}

if ($input['edit']) {
    $input['form']['content'] = str_replace('text_area', 'textarea', $input['form']['content']);
    $query = "update templates set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query, null, true);
    if($input['update']){
        $input['view']=1;
    }
}

if (($input['view']) || ($input['adding'])) {
    if ($input['view']) {
        $query = "select * from templates where id='{$input['id']}'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = 'edit';
	$tags['form_title'] = 'Редактирование';
    } else {
	$tags['type'] = 'add';
	$tags['form_title'] = 'Добавление';
    }
    $tags[content] = str_replace('textarea', 'text_area', $tags[content]);
    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $EDITOR_HTML_INC;
    $content.=get_tpl_by_title('templates_edit_form', $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, '', $content);
} else {
    $query = "SELECT * from templates order by title asc";
    $result = my_query($query, $conn);
    $content.=get_tpl_by_title('templates_edit_table', $tags, $result);
    echo get_tpl_by_title($part[tpl_name], $tags, '', $content);
}

