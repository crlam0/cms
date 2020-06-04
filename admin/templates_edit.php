<?php

$tags['Header'] = 'Шаблоны';
include '../include/common.php';
header('X-XSS-Protection:0');

if ($input['del']) {
    $query = "delete from templates where id='{$input['id']}'";
    my_query($query);
}

function twig_tpl_load($filename){
    global $DIR;
    if(!strstr($filename,'.html.twig')) {
        $filename.='.html.twig';
    }
    $filename = $DIR . 'templates/' . $filename;
    if(file_exists($filename)) {
        return file_get_contents($filename);
    } else {
        return '';
    }    
}

function twig_tpl_save($filename, $content){
    global $DIR;
    if(!strstr($filename,'.html.twig')) {
        $filename.='.html.twig';
    }
    $filename = $DIR . 'templates/' . $filename;
    if(!strlen($content)) {
        if(file_exists($filename)) {
            return true;
        }
        $content = PHP_EOL;
    }
    return file_put_contents($filename, stripcslashes($content)) && clear_cache_dir('twig');
}

if($input['revert']){
    unset($input['edit']);
    $input['view']=1;
}

if ($input['add']) {
    if($input['form']['template_type']==='twig' && strlen($input['form']['file_name'])) {
        if(!twig_tpl_save($input['form']['file_name'],$input['form']['content'])) {
            $content.=my_msg_to_str('error', [], 'Ошибка сохранения файла шаблона.');
        }
    }    
    $query = "insert into templates " . db_insert_fields($input['form']);
    my_query($query);
    $input['view'] = true;
    $input['id'] = $mysqli->insert_id;    
}

if ($input['edit']) {    
    if($input['form']['template_type']==='twig' && strlen($input['form']['file_name'])) {
        if(!twig_tpl_save($input['form']['file_name'],$input['form']['content'])) {
            $content.=my_msg_to_str('error', [], 'Ошибка сохранения файла шаблона.');
        }
    }    
    $query = "update templates set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    if($input['update']){
        $input['view']=1;
    }
}

if (($input['view']) || ($input['adding'])) {
    if ($input['view']) {
        $query = "select * from templates where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = 'edit';
	$tags['form_title'] = 'Редактирование';
        if($tags['template_type']==='twig' && strlen($tags['file_name'])) {
            $tags['content'] = twig_tpl_load($tags['file_name']);
        }        
    } else {
	$tags['type'] = 'add';
	$tags['form_title'] = 'Добавление';
	$tags['content'] = '';
	$tags['template_type'] = 'my';
    }
    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $EDITOR_HTML_INC;
    $tags['template_type_select'] = 
            '<option value="my"'.($tags['template_type']==='my' ? ' selected' : '').'>My</option>' . 
            '<option value="twig"'.($tags['template_type']==='twig' ? ' selected' : '').'>Twig</option>';
    $content.=get_tpl_by_name('templates_edit_form', $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
} else {
    $query = "SELECT * from templates order by name asc";
    $result = my_query($query);
    $content.=get_tpl_by_name('templates_edit_table', $tags, $result);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
}

