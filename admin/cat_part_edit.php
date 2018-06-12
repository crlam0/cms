<?php

$tags['Header'] = 'Каталог';
include '../include/common.php';

/*
$query = "select * from cat_part";
$result = my_query($query, $conn, 1);
while ($row = $result->fetch_array()) {
    $query="update cat_part set seo_alias='".encodestring($row["title"])."' where id='{$row["id"]}'";
    my_query($query, $conn, 1);
}
*/

function prev_part($prev_id, $deep, $arr) {
    if($deep > 1) {
        return null;
    }
    $query = "SELECT id,title,prev_id from cat_part where id='{$prev_id}' order by num,title asc";
    $result = my_query($query, null, true);
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

if ($input["del"]) {
    $query = "select count(id) as cnt from cat_part where prev_id=" . $input['id'] . " having cnt>0
	union select count(id) as cnt from cat_item where part_id=" . $input['id'] . " having cnt>0";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
        $content.=my_msg_to_str('error','','Этот раздел не пустой !');
    } else {
	list($img) = my_select_row("select img from cat_part where id=" . $input['id']);
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img))$content.=my_msg_to_str('error','','Ошибка удаления файла !');
	}
	$query = "delete from cat_part where id=" . $input['id'];
	my_query($query, $conn);
    }
}

if ($input["del_img"]) {
    list($img) = my_select_row("select img from cat_part where id=" . $input['id']);
    if (is_file($IMG_PATH . $img)) {
	if (!unlink($IMG_PATH . $img)){
            $content.=my_msg_to_str('error','','Ошибка удаления файла !');
        }    
    }
    $query = "update cat_part set img='-' where id=" . $input['id'];
    my_query($query, $conn);
    $input["edit"] = 1;
    $content.=my_msg_to_str('','','Изображение удалено');
}

if ($input["copy"]) {
    $query = "SELECT * from cat_part where prev_id={$input['id']} order by num,title+1 asc";
    $result_part = my_query($query, $conn);
    while ($row_part = $result_part->fetch_assoc()) {
        $row_part['title'];
        $input_cat_id = $row_part['id'];
        unset($row_part['id']);
        $row_part['prev_id']=$input['to_part_id'];
        $query = "insert into cat_part " . db_insert_fields($row_part);
        my_query($query, null, false);
        $part_id=$mysqli->insert_id;        
        $seo_alias=encodestring($row_part['title']).'_'.$part_id;        
        my_query("update cat_part set seo_alias='{$seo_alias}' where id='{$part_id}'");
        
        $query = "SELECT * from cat_item where part_id='{$input_cat_id}'";
        $result_item = my_query($query, $conn);
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
    $content.=my_msg_to_str('','','Раздел успешно скопирован.');
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
    }
    $query = "insert into cat_part " . db_insert_fields($input['form']);
    my_query($query, $conn, 0);
    $insert_id=$mysqli->insert_id;
    if($seo_alias_duplicate){
        $input['form']['seo_alias'].='_'.$insert_id;
        my_query("update cat_part set seo_alias='{$input['form']['seo_alias']}' where id='{$insert_id}'");
    }
    $content.=my_msg_to_str('','','Раздел успешно добавлен.');
    if ($_FILES['img_file']['size']) {
	$part_id = mysql_insert_id($conn);
	$f_info = pathinfo($_FILES['img_file']['name']);
	$img = $part_id . '.' . $f_info['extension'];
//		if(move_uploaded_file($_FILES["img_file"]["tmp_name"],$IMG_PATH.$img)){
	if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings["catalog_part_img_max_width"])) {
	    $query = "update cat_part set img='{$img}' where id='{'$part_id'}'";
	    my_query($query, $conn);
	} else {
            $content.=my_msg_to_str('error','','Ошибка копирования файла !');
	}
    }
}

if ($input['edited']) {
    if (!strlen($input['form']['seo_alias']))$input['form']['seo_alias'] = encodestring($input['form']['title']);
    $num_rows=my_select_row("select id from cat_part where seo_alias='{$input['form']['seo_alias']}' and id<>'{$input['id']}'", false);
    if($num_rows>1){
        $input['form']['seo_alias'].='_'.$input['id'];
    }
    $query = "update cat_part set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query, $conn, 1);
    $content.=my_msg_to_str('','','Раздел успешно изменен.');
    if ($_FILES["img_file"]["size"] > 100) {
	list($img) = my_select_row("select img from cat_part where id='{$input['id']}'");
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img))$content.=my_msg_to_str('error','','Ошибка удаления файла !');
	}
	$f_info = pathinfo($_FILES["img_file"]["name"]);
	$img = $input['id'] . "." . $f_info["extension"];
//		if(move_uploaded_file($_FILES["img_file"]["tmp_name"],$IMG_PATH.$img)){
	if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings['catalog_part_img_max_width'])) {
	    $query = "update cat_part set img='{$img}' where id='{$input['id']}'";
	    my_query($query, $conn);
	} else {
	   $content.=my_msg_to_str('error','','Ошибка копирования файла !');
	}
    }
}

if (($input['edit']) || ($input['adding'])) {
    if ($input['edit']) {
	$query = "select * from cat_part where id='{$input['id']}'";
	$result = my_query($query, null, true);
	$tags = $result->fetch_array();
	$tags['form_title'] = 'Редактирование';
	$tags['type'] = 'edited';
	$tags['Header'] = 'Редактирование раздела';
    } else {
	$tags['price_cnt'] = 1;
	$tags['price1_title'] = 'Цена, руб.';
	$tags['price2_title'] = 'Цена 2, руб.';
	$tags['price3_title'] = 'Цена 3, руб.';
	$tags['form_title'] = "Добавление";
	$tags['type'] = "added";
	$tags['Header'] = "Добавление раздела";
    }
    $tags['head_inc'] = $EDITOR_SIMPLE_INC;
    
    $prev_id_select = '';
    function sub_part_select($prev_id, $deep) {
        global $prev_id_select, $tags;
        if($deep>2) {
            return null;
        }
        $query = "select * from cat_part where prev_id='{$prev_id}' order by num,title asc";
        $result = my_query($query, null, true);

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
            $prev_id_select.="<option value={$row['id']}" . ($tags['prev_id'] == $row['id'] ? " selected" : "") . ">{$row['title']}</option>\n";
            sub_part_select($row['id'], $deep + 1);
        }
    }
    sub_part_select(0, 0);
    
    $tags['prev_id_select'] = $prev_id_select;

    $tags['img_tag'] = (is_file($IMG_PATH . $tags['img']) ? "<img src=../{$settings['catalog_part_img_path']}{$tags['img']} class=margin><br>" : "[ Отсутствует ]<br>");
    $tags['del_button'] = (is_file($IMG_PATH . $tags['img']) ? "<a href=" . $_SERVER['PHP_SELF'] . "?del_img=1&id={$tags['id']}>Удалить</a><br>" : '');

    $content .= get_tpl_by_title('cat_part_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
};

function sub_part($prev_id, $deep) {
    global $conn, $IMG_PATH, $tags, $IMG_URL;
    $query = "SELECT * from cat_part where prev_id={$prev_id} order by num,title+1 asc";
    $result = my_query($query, null, true);
    while ($row = $result->fetch_array()) {
	$spaces = '';
        $spaces = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $deep);
	$tags['table_content'].="
            <tr class=content align=left>
                <td>{$spaces}<a href=cat_item_edit.php?part_id={$row['id']}>{$row['num']} {$row['title']}</a></td>
                <td>{$row['seo_alias']}</a></td>
                <td align=center>" . (is_file($IMG_PATH . $row['img']) ? "<img src={$IMG_URL}{$row['img']} border=0>" : "&nbsp;") . "</td>
                <td width=16><a href=" . $_SERVER['PHP_SELF'] . "?edit=1&id={$row['id']}><img src=\"../images/open.gif\" width=16 height=16 alt=\"Редактировать\" border=0></a></td>
                <td width=16><a href=" . $_SERVER['PHP_SELF'] . "?del=1&id={$row['id']}><img src=\"../images/del.gif\" alt=\"Удалить\" border=0 onClick=\"return test()\"></a></td>
            </tr>
            ";
	sub_part($row['id'], $deep + 1);
    }
}

$tags['table_content'] = '';
sub_part(0, 0);
$content .= get_tpl_by_title('cat_part_table', $tags);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);

