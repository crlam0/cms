<?php

$tags['Header'] = 'Каталог';
include '../include/common.php';

use classes\App;

if (isset($input['part_id'])) {
    $_SESSION['ADMIN_PART_ID'] = $input['part_id'];
}

list($part_title) = my_select_row("select title from cat_part where id='{$_SESSION['ADMIN_PART_ID']}'", 1);
$tags['Header'].=" -> $part_title";

$IMG_PATH = $DIR . $settings['catalog_item_img_path'];
$IMG_URL = $SUBDIR . $settings['catalog_item_img_path'];

function show_img($tmp, $row): string
{
    global $IMG_PATH, $IMG_URL;
    if (is_file($IMG_PATH . $row['fname'])) {
        return "<a href={$IMG_URL}{$row['fname']}><img src={$IMG_URL}{$row['fname']} border=0 width=150></a>";
    } else {
        return "Отсутствует";
    }
}

function get_image_list($item_id): string
{
    global $IMG_URL;
    $query = "select cat_item_images.*,default_img,cat_item.id as item_id from cat_item_images left join cat_item on (cat_item.id=item_id) where item_id='$item_id'";
    $result = my_query($query);
    $content = "<table width=550 border=0 cellspacing=1 cellpadding=1 class=\"table table-striped table-responsive table-bordered normal-form\" align=center >
	<tr class=header align=center>
		<td width=15%>По умолчанию</td>
		<td width=45%>Изображение</td>
		<td width=25%>Описание</td>
		<td width=15%>Удалить</td>
	</tr>";
    while ($row = $result->fetch_array()) {
        $content.="<tr class=content valign=middle align=left>
                <td align=center><input type=radio name=ch_default class=default_img image_id={$row['id']}" . ($row['default_img'] == $row['id'] ? " checked" : "") . "></td>
                <td align=center><img src={$IMG_URL}{$row['fname']} border=0 width=150></td>
                <td><span id=descr_{$row['id']}>{$row['descr']}</span></td>
                <td align=center><a href=# class=del_button image_id={$row['id']} alt=\"Удалить\"><img src=\"" . App::$SUBDIR . "admin/images/del.gif\"  border=0></a></td>
		</tr>";
    }
    $content.="</table>";
    return $content;
}

if ($input['get_image_list']) {
    echo get_image_list($input['item_id']);
    exit;
}

if ($input['default_img']) {
    $query = "update cat_item set default_img='" . $input['default_img'] . "' where id=" . $input['item_id'];
    echo (my_query($query, null, true) ? 'OK' : mysql_error() );
    exit;
}

if ($input['del_image']) {
    list($fname) = my_select_row('select fname from cat_item_images where id=' . $input['id'], 1);
    $query = "delete from cat_item_images where id=" . $input["id"];
    echo (my_query($query, null, true) ? 'OK' : mysql_error() );
    $result = @unlink($IMG_PATH . $fname);
    if (!$result) {
        echo 'Error delete file !';
    }
    exit;
}

if ($input['del']) {
    $query = 'select * from cat_item_images where item_id=' . $input['id'];
    $result = my_query($query);
    while ($row = $result->fetch_array()) {
        if (!unlink($IMG_PATH . $row['fname'])) {
            $content.=my_msg_to_str('error', [], 'Ошибка удаления файла !');
        }
    }
    $query = 'delete from cat_item_images where item_id=' . $input['id'];
    my_query($query);
    $query = 'delete from cat_item where id=' . $input['id'];
    my_query($query);
    $content.=my_msg_to_str('', [], 'Наименование удалено');
}

if ($input['add_image']) {
    if ($_FILES['img_file']['size']) {
        $query = "insert into cat_item_images(date_add,item_id,descr) values(now(),'{$input["id"]}','{$input["descr"]}')";
        my_query($query);
        $image_id = App::$db->insert_id();
        $f_info = pathinfo($_FILES['img_file']['name']);
        $img = $input['id'] . '_' . $image_id . '.' . $f_info['extension'];

        list($item_image_width,$item_image_height) = my_select_row("
                select item_image_width,item_image_height from cat_item
                left join cat_part on (cat_part.id=cat_item.part_id)
                where cat_item.id = '{$input['id']}'", false);

        $upload_result=false;
        if ($item_image_width>0 && $item_image_height>0) {
            $upload_result = move_uploaded_image($_FILES['img_file'], $IMG_PATH . $img, null, null, $item_image_width, $item_image_height);
        } else {
            $upload_result = move_uploaded_image($_FILES['img_file'], $IMG_PATH . $img, $settings['catalog_item_img_max_width']);
        }

        if ($upload_result) {
            $query = "update cat_item_images set fname='{$img}',file_type='" . $_FILES['img_file']['type'] . "' where id='{$image_id}'";
            my_query($query);
            $query = "select id from cat_item_images where item_id='{$input['id']}'";
            $result = my_query($query);
            if ($result->num_rows==1) {
                $query = "update cat_item set default_img='{$image_id}' where id='{$input['id']}'";
                my_query($query);
            }
        } else {
            $query = "delete from cat_item_images where id='{$image_id}'";
            my_query($query);
            $content.=my_msg_to_str('error', [], 'Ошибка копирования файла !');
        }
        $input['edit']=1;
        $input['id']=$input['id'];
    }
    exit;
}

if (is_array($input['form'])) {
    foreach ($input['form'] as $key => $value) {
        $input['form'][$key] = trim($value);
    }
}

if ($input['added']) {
    if (!strlen($input['form']['num'])) {
        list($input['form']['num'])=my_select_row("select max(num) from cat_item where part_id='{$_SESSION['ADMIN_PART_ID']}'", false);
        $input['form']['num']++;
    }
    if (!strlen($input['form']['seo_alias'])) {
        $input['form']['seo_alias'] = encodestring($input['form']['title']);
    }
    $num_rows=my_select_row("select id from cat_item where seo_alias='{$input['form']['seo_alias']}'", true);
    $seo_alias_duplicate = false;
    if ($num_rows>0) {
        $seo_alias_duplicate=1;
    }
    $input['form']['part_id'] = $_SESSION['ADMIN_PART_ID'];

    if ($input['props']) {
        $props_array=[];
        foreach ($input['props'] as $name => $value) {
            $props_array[$name] = $value;
        }
        $json = json_encode($props_array);
        if (json_last_error() == JSON_ERROR_NONE) {
            $input['form']['props'] = $json;
        } else {
            $content.=my_msg_to_str('', [], json_last_error_msg());
        }
    }

    $input['form']['date_add']='now()';
    $input['form']['date_change']='now()';
    $query = "insert into cat_item " . db_insert_fields($input['form']);
    my_query($query);
    $insert_id=App::$db->insert_id();
    if ($seo_alias_duplicate) {
        $input['form']['seo_alias'].='_'.$insert_id;
        my_query("update cat_item set seo_alias='{$input['form']['seo_alias']}' where id='{$insert_id}'");
    }
    $content.=my_msg_to_str('', [], 'Наменование добавлено !');
    $input['id']=$insert_id;
    $input['edit']=1;
}

if ($input['edited']) {
    $input['form']['part_id'] = $_SESSION['ADMIN_PART_ID'];

    if ($input['props']) {
        $props_array=[];
        foreach ($input['props'] as $name => $value) {
            $props_array[$name] = $value;
        }
        $json = json_encode($props_array);
        if (json_last_error() == JSON_ERROR_NONE) {
            $input['form']['props'] = $json;
        } else {
            $content.=my_msg_to_str('', [], json_last_error_msg());
        }
    }

    if (!strlen($input['form']['seo_alias'])) {
        $input['form']['seo_alias'] = encodestring($input['form']['title']);
    }
    $num_rows=my_select_row("select id from cat_item where seo_alias='{$input['form']['seo_alias']}' and id<>{$input['id']}", false);
    if ($num_rows>1) {
        $input['form']['seo_alias'].='_'.$input['id'];
    }
    $input['form']['date_change']='now()';
    $query = "update cat_item set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', [], 'Изменения сохранены');
    $input['edit']=1;
}

if (($input['edit']) || ($input['add'])) {
    if ($input['edit']) {
        $query = "select * from cat_item where id='{$input['id']}'";
        $result = my_query($query);
        $tags=array_merge($tags, $result->fetch_array());
        $tags['form_title'] = 'Редактирование';
        $tags['type'] = 'edited';
        $tags['Header'] = 'Редактирование товара';
        $tags['descr']=  strip_tags($tags['descr']);
    } else {
        $tags['form_title'] = 'Добавление';
        $tags['type'] = 'added';
        $tags['Header'] = 'Добавление товара';
        $tags['price'] = '';
    }
    $row_part = my_select_row("select * from cat_part where id=" . $_SESSION['ADMIN_PART_ID'], 1);

    $tags['props_inputs']='';
    if (strlen($row_part['items_props'])) {
        $props_array = my_json_decode($row_part['items_props']);
        if (!is_array($props_array)) {
            $content.=my_msg_to_str('', [], 'Массив свойств неверен');
        } else {
            $props_values=my_json_decode($tags['props']);
            if (is_array($props_values)) {
                foreach ($props_values as $input_name => $value) {
                    $param_value[$input_name]=$value;
                }
            }
            foreach ($props_array as $input_name => $params) {
                $tags['props_inputs'] .= ''
                        . '<tr class=content align=left>'
                        . '<td>'.$params['name'].'</td><td>' . PHP_EOL;
                if (check_key('type', $params) == 'boolean') {
                    $tags['props_inputs'] .= '<input type="checkbox" maxlength="45" size="64" name="props['.$input_name.']" '.(check_key($input_name, $param_value) ? ' checked' : '').'>';
                } else {
                    $tags['props_inputs'] .= '<input type="edit" class="form-control" maxlength="45" size="64" name="props['.$input_name.']" value="'.check_key($input_name, $param_value).'">';
                }
                $tags['props_inputs'] .= '</td></tr>' . PHP_EOL;
            }
        }
    }

    $tags['price_inputs'] = "<tr class=content align=left><td>{$row_part['price_title']}</td><td><input type=edit class=form-control maxlength=45 size=64 name=form[price] value=\"{$tags['price']}\"></td></tr>";

//  $tags[images]=get_image_list($input["id"]);

    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $JQUERY_FORM_INC . $EDITOR_MINI_INC;
    $content .= get_tpl_by_name('cat_item_form', $tags);
    if ($input['edit']) {
        $content .= get_tpl_by_name('cat_item_images_form', $tags);
    }
    echo get_tpl_by_name($part['tpl_name'], $tags, null, $content);
    exit;
}

$query = "SELECT cat_item.*,fname from cat_item
left join cat_item_images on (cat_item_images.id=default_img)
where part_id='{$_SESSION['ADMIN_PART_ID']}' order by num,cat_item.id,title asc";
$result = my_query($query);

$row_part = my_select_row("select * from cat_part where id=" . $_SESSION['ADMIN_PART_ID'], 1);
$tags['price_header']="<td width=10%>{$row_part['price_title']}</td>";

function price_content($tmp, $row): string
{
    $row_part = my_select_row("select * from cat_part where id='{$_SESSION['ADMIN_PART_ID']}'", true);
    $content = "<td align=center>{$row['price']}</td>";
    return $content;
}

$content = get_tpl_by_name('cat_item_table', $tags, $result);
echo get_tpl_by_name($part['tpl_name'], $tags, null, $content);
