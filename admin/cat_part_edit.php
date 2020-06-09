<?php

$tags['Header'] = 'Каталог';
include '../include/common.php';

use classes\App;

/*
$query = "select * from cat_part";
$result = my_query($query);
while ($row = $result->fetch_array()) {
    $query="update cat_part set seo_alias='".encodestring($row["title"])."' where id='{$row["id"]}'";
    my_query($query);
}
*/
if (isset($input['part_id'])) {
    $part_id = intval($input['part_id']);
    $item_id = intval($input['item_id']);
    $value = strlen($input['value']);

    list($json_row) = my_select_row("select related_products from cat_part where id='{$part_id}'", true);
    if(!$related_products = my_json_decode($json_row)) {
        $related_products=[];
    }
    if($value>0) {
        $related_products[$item_id] = 'true';
    } else {
        unset($related_products[$item_id]);
    }
    $json = json_encode($related_products);
    $query = "update cat_part set related_products='{$json}' where id='{$part_id}'";
    $result = my_query($query);    
    if($result) {
        echo 'OK';
    } else {
        echo 'SQL Error: ' . $query;
    }
    exit;
}


if($input['get_related_products_list']) {
    $content = '<input type="hidden" id="part_id" value="'.$input['id'].'"';
    list($json_row) = my_select_row("select related_products from cat_part where id='{$input['id']}'", true);
    if(!$related_products = my_json_decode($json_row)) {
        $related_products=[];
    }
    $query = "SELECT id,title from cat_part order by num,title asc";
    $result = my_query($query);
    while($row=$result->fetch_array()){
        $content .= '        
        <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading" data-toggle="collapse" href="#collapse'.$row['id'].'">
            <h4 class="panel-title">
              '.$row['title'].'
            </h4>
          </div>
          <div id="collapse'.$row['id'].'" class="panel-collapse collapse">
            <ul class="list-group">';
            $query = "SELECT cat_item.* from cat_item where part_id='{$row['id']}' order by num,title asc";
            $result_item = my_query($query);
            while($row_item=$result_item->fetch_array()){
                $state = '';
                if(array_key_exists($row_item['id'], $related_products)) {
                    $state = ' checked';
                }
                $content .= '<li class="list-group-item">'.$row_item['title'].
                        '<input type="checkbox" class="related_products_input" item_id="'.$row_item['id'].'" '.$state.' />'
                        . '</li>';
            }
            $content .= '
            </ul>
          </div>
        </div>
        </div>';
    }
    $json['content'] = $content;
    $json['result'] = 'OK';    
    echo json_encode($json);
    exit;
}

function prev_part($prev_id, $deep, $arr) {
    if($deep > 1) {
        return null;
    }
    $query = "SELECT id,title,prev_id from cat_part where id='{$prev_id}' order by num,title asc";
    $result = my_query($query);
    if(!$result->num_rows){
        return null;
    }
    $arr[$deep] = $result->fetch_array();
    if ($arr[$deep]['prev_id']){
        $arr = prev_part($arr[$deep]['prev_id'], $deep + 1, $arr);
    }    
    return $arr;
}

$IMG_PATH = $DIR . $settings['catalog_part_img_path'];
$IMG_URL = $BASE_HREF . $settings['catalog_part_img_path'];

if ($input['del']) {
    $query = "select count(id) as cnt from cat_part where prev_id='" . $input['id'] . "' having cnt>0
	union select count(id) as cnt from cat_item where part_id='" . $input['id'] . "' having cnt>0";
    $result = my_query($query);
    if ($result->num_rows) {
        $content.=my_msg_to_str('error', [],'Этот раздел не пустой !');
    } else {
        list($img) = my_select_row("select image_name from cat_part where id='{$input['id']}'");
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img))$content.=my_msg_to_str('error', [], 'Ошибка удаления файла !');
	}
	$query = "delete from cat_part where id=" . $input['id'];
	my_query($query);
    }
}

if ($input['del_img']) {
    list($img) = my_select_row("select image_name from cat_part where id='{$input['id']}'");
    if (is_file($IMG_PATH . $img)) {
	if (!unlink($IMG_PATH . $img)){
            $content.=my_msg_to_str('error', [],'Ошибка удаления файла !');
        }    
    }
    $query = "update cat_part set image_name='-' where id=" . $input['id'];
    my_query($query);
    $input["edit"] = 1;
    $content.=my_msg_to_str('',[],'Изображение удалено');
}

if ($input["copy"]) {
    $query = "SELECT * from cat_part where prev_id={$input['id']} order by num,title+1 asc";
    $result_part = my_query($query);
    while ($row_part = $result_part->fetch_assoc()) {
        $row_part['title']; // IT'S A BRILLIANT !!!!!!!!!!!!!!
        $input_cat_id = $row_part['id'];
        unset($row_part['id']);
        $row_part['prev_id']=$input['to_part_id'];
        $query = "insert into cat_part " . db_insert_fields($row_part);
        my_query($query, null, false);
        $part_id=$mysqli->insert_id;        
        $seo_alias=encodestring($row_part['title']).'_'.$part_id;        
        my_query("update cat_part set seo_alias='{$seo_alias}' where id='{$part_id}'");
        
        $query = "SELECT * from cat_item where part_id='{$input_cat_id}'";
        $result_item = my_query($query);
        while ($row_item = $result_item->fetch_assoc()) {
            unset($row_item['id']);
            $row_item['part_id'] = $part_id;
            $query = "insert into cat_item " . db_insert_fields($row_item);
            my_query($query, null, false);
            $insert_id=$mysqli->insert_id;        
            $seo_alias=encodestring($row_item['title']).'_'.$insert_id;
            my_query("update cat_item set seo_alias='{$seo_alias}' where id='{$insert_id}'");
        }
    }
    $content.=my_msg_to_str('',[],'Раздел успешно скопирован.');
}



if ($input["added"]) {
    if(!strlen($input["form"]["num"])){
        list($input["form"]["num"])=my_select_row("select max(num) from cat_part where prev_id='{$input['form']['prev_id']}'",0);
        $input["form"]["num"]++;
    }
    if (!strlen($input['form']['seo_alias']))$input['form']['seo_alias'] = encodestring($input['form']['title']);
    $num_rows=my_select_row("select id from cat_part where seo_alias='{$input['form']['seo_alias']}'",1);
    if($num_rows>0){
        $seo_alias_duplicate=1;
    } else {
        $seo_alias_duplicate = 0;
    }
    $input['form']['date_add']='now()';
    $input['form']['date_change']='now()';
    $query = "insert into cat_part " . db_insert_fields($input['form']);
    my_query($query);
    $insert_id=$mysqli->insert_id;
    if($seo_alias_duplicate){
        $input['form']['seo_alias'].='_'.$insert_id;
        my_query("update cat_part set seo_alias='{$input['form']['seo_alias']}' where id='{$insert_id}'");
    }
    $content.=my_msg_to_str('',[],'Раздел успешно добавлен.');
    if ($_FILES['img_file']['size']) {
	$part_id = $insert_id;
	$f_info = pathinfo($_FILES['img_file']['name']);
	$img = $part_id . '.' . $f_info['extension'];
	if (move_uploaded_image($_FILES['img_file'], $IMG_PATH . $img, $settings['catalog_part_img_max_width'])) {
	    $query = "update cat_part set image_name='{$img}',image_type='{$_FILES['img_file']['type']}' where id='{$part_id}'";
	    my_query($query);
	} else {
            $content.=my_msg_to_str('error', [],'Ошибка копирования файла !');
	}
    }
}

if ($input['edited']) {
    if(isset($input['form']['prev_id']) && $input['form']['prev_id'] == $input['id']) {
        $input['form']['prev_id'] = 0;
    }
    if (!strlen($input['form']['seo_alias'])){
        $input['form']['seo_alias'] = encodestring($input['form']['title']);
    }
    $num_rows=my_select_row("select id from cat_part where seo_alias='{$input['form']['seo_alias']}' and id<>'{$input['id']}'", false);
    if($num_rows>1){
        $input['form']['seo_alias'].='_'.$input['id'];
    }
    $input['form']['date_change']='now()';
    $query = "update cat_part set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('',[],'Раздел успешно изменен.');
    if ($_FILES['img_file']['size'] > 100) {
	list($img) = my_select_row("select image_name from cat_part where id='{$input['id']}'");
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img))$content.=my_msg_to_str('error', [],'Ошибка удаления файла !');
	}
	$f_info = pathinfo($_FILES['img_file']['name']);
	$img = $input['id'] . "." . $f_info['extension'];
//		if(move_uploaded_file($_FILES["img_file"]["tmp_name"],$IMG_PATH.$img)){
	if (move_uploaded_image($_FILES['img_file'], $IMG_PATH . $img, $settings['catalog_part_img_max_width'])) {
	    $query = "update cat_part set image_name='{$img}',image_type='{$_FILES['img_file']['type']}' where id='{$input['id']}'";
	    my_query($query);
	} else {
	   $content.=my_msg_to_str('error', [],'Ошибка копирования файла !');
	}
    }
}


if (($input['edit']) || ($input['adding'])) {
    if ($input['edit']) {
	$query = "select * from cat_part where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_assoc());
	$tags['form_title'] = 'Редактирование';
	$tags['type'] = 'edited';
	$tags['Header'] = 'Редактирование раздела';
    } else {
	$tags['price_title'] = 'Цена, руб.';
	$tags['form_title'] = "Добавление";
	$tags['type'] = "added";
	$tags['Header'] = "Добавление раздела";
        if(isset(App::$settings['catalog']['default_items_props'])){
            $tags['items_props'] = App::$settings['catalog']['default_items_props'];
        }        
    }
    $tags['INCLUDE_HEAD'] = $EDITOR_MINI_INC . $EDITOR_HTML_INC;
    $prev_id_select = '';
    function sub_part_select($prev_id, $deep) {
        global $prev_id_select, $tags;
        if($deep>2) {
            return null;
        }
        $query = "select * from cat_part where prev_id='{$prev_id}' order by num,title asc";
        $result = my_query($query);
        $arr = null;
        while ($row = $result->fetch_array()) {
            $title='';
            $arr=prev_part($row['prev_id'], 0, $arr);
            if(is_array($arr)){
                $arr = array_reverse($arr);
                foreach($arr as $value){
                    $title.=$value['title'] . ' -> ';
                }
            }
            $row['title'] = $title . $row['title'];
            $prev_id_select.="<option value={$row['id']}" . (isset($tags['prev_id']) && $tags['prev_id'] == $row['id'] ? " selected" : "") . ">{$row['title']}</option>\n";
            sub_part_select($row['id'], $deep + 1);
        }
    }
    sub_part_select(0, 0);
    
    $tags['prev_id_select'] = $prev_id_select;

    $tags['img_tag'] = (isset($tags['image_name']) && is_file($IMG_PATH . $tags['image_name']) ? "<img src=../{$settings['catalog_part_img_path']}{$tags['image_name']} class=margin><br>" : "[ Отсутствует ]<br>");
    $tags['del_button'] = (isset($tags['image_name']) && is_file($IMG_PATH . $tags['image_name']) ? "<a href=" . $_SERVER['PHP_SELF'] . "?del_img=1&id={$tags['id']}>Удалить</a><br>" : '');

    $content .= get_tpl_by_name('cat_part_form', $tags);
    echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
    exit;
};

function sub_part($prev_id, $deep) {
    global $IMG_PATH, $tags, $IMG_URL;
    $query = "SELECT * from cat_part where prev_id={$prev_id} order by num,title+1 asc";
    $result = my_query($query);
    while ($row = $result->fetch_array()) {
	$spaces = '';
        $spaces = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $deep);
	$tags['table_content'].="
            <tr class=content align=left>
                <td>{$spaces}<a href=cat_item_edit.php?part_id={$row['id']}>{$row['num']} {$row['title']}</a></td>
                <td>{$row['seo_alias']}</a></td>
                <td align=center>" . (is_file($IMG_PATH . $row['image_name']) ? "<img src={$IMG_URL}{$row['image_name']} border=0>" : "&nbsp;") . "</td>
                <td width=16><a href=" . $_SERVER['PHP_SELF'] . "?edit=1&id={$row['id']}><img src=\"../images/open.gif\" width=16 height=16 alt=\"Редактировать\" border=0></a></td>
                <td width=16><a href=" . $_SERVER['PHP_SELF'] . "?del=1&id={$row['id']}><img src=\"../images/del.gif\" alt=\"Удалить\" border=0 onClick=\"return test()\"></a></td>
            </tr>
            ";
	sub_part($row['id'], $deep + 1);
    }
}

$tags['table_content'] = '';
sub_part(0, 0);
$content .= get_tpl_by_name('cat_part_table', $tags);
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);

