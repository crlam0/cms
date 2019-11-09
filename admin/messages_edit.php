<?php
$tags['Header']='Сообщения';
include '../include/common.php';

use Classes\App;

if ($input['del']){
	$query="delete from messages where id='{$input['id']}'";
	App::$db->query($query);
}

if ($input['add']){	
	$query="insert into messages ".db_insert_fields($input['form']);
	App::$db->query($query);
}


if ($input['edit']){	
	$query="update messages set ".db_update_fields($input['form'])." where id='{$input['id']}'";
	App::$db->query($query);
}

if (($input['view'])||($input['adding'])){
	if($input['view']){
            $query="select * from messages where id='{$input['id']}'";
            $result=my_query($query);
            $tags=array_merge($tags,$result->fetch_array());
        	$tags['form_type']='edit';
        	$tags['form_title']='Редактирование';
        }else{
        	$tags['form_type']='add';
        	$tags['form_title']='Добавление';
                $tags['type']='info';
        }
        $tags['types']="<select name=form[type]>
        <option value=info".($tags['type']=="info"?" selected":"").">Info</option>
        <option value=notice".($tags['type']=="notice"?" selected":"").">Notice</option>
        <option value=error".($tags['type']=="error"?" selected":"").">Error</option>
        </select>";        
        $content.=get_tpl_by_name('messages_edit_form',$tags);
        echo get_tpl_default($tags,"",$content);  
}else{

	$query="SELECT * from messages order by name asc";
	$result=App::$db->query($query);
	$content.=get_tpl_by_name('messages_edit_table',$tags,$result);
	echo get_tpl_default($tags,'',$content);	
}

