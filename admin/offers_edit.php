<?php

$tags['Header'] = 'Картинки для спец. предложений';
include '../include/common.php';

$image_path = $settings['offers_upload_path'];
$TABLE = 'offers_images';

function show_img($tmp, $row) {
    global $DIR, $image_path;
    if (is_file($DIR . $image_path . $row['file_name'])) {
        return '<img src="../'.$image_path.$row['file_name'].'" border="0" width="200"></a>';
    } else {
        return 'Отсутствует';
    }
}

if ($input['del_image']) {
    list($img_old) = my_select_row("select file_name from {$TABLE} where id='{$input['id']}'");
    if (is_file($DIR . $image_path . $img_old)) {
        if (!unlink($DIR . $image_path . $img_old)){
            $content.=my_msg_to_str('error', '', 'Ошибка удаления файла');
        }    
    }
    $query = "delete from {$TABLE} where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', '', 'Изображение успешно удалено.');
}

// ($_FILES["img_file"]);

if ($input['added_image']) {
    $query = "insert into {$TABLE} " . db_insert_fields($input['form']);
    my_query($query, $conn);
    if ($_FILES['img_file']['size'] > 100) {
        if (!in_array($_FILES['img_file']['type'], $validImageTypes)) {
            $content.=my_msg_to_str('error', '', 'Неверный тип файла !');
        } else {
            $image_id = $mysqli->insert_id;
            $f_info = pathinfo($_FILES['img_file']['name']);
            $file_name = encodestring($input['form']['title']) . '.' . $f_info['extension'];
            if (move_uploaded_image($_FILES['img_file'], $DIR . $image_path . $file_name, null, 475)) {
                $query = "update {$TABLE} set file_name='{$file_name}',file_type='" . $_FILES['img_file']['type'] . "' where id='{$image_id}'";
                my_query($query, $conn);
                $content.=my_msg_to_str('', '', 'Изображение успешно добавлено.');
            } else {
                $content.=my_msg_to_str('error', '', 'Ошибка копирования файла !');
            }
        }
    }
}

if ($input['edited_image']) {
    if ($_FILES['img_file']['size'] > 100) {
        if (!in_array($_FILES['img_file']['type'], $validImageTypes)) {
            $content.=my_msg_to_str('error', '', 'Неверный тип файла !');
        } else {
            list($img_old) = my_select_row("select file_name from {$TABLE} where id='{$input['id']}'");
            if (is_file($DIR . $image_path . $img_old)) {
                if (!unlink($DIR . $image_path . $img_old))
                    $content.=my_msg_to_str('error', '', 'Ошибка удаления файла');
            }
            $f_info = pathinfo($_FILES['img_file']['name']);
            $file_name = encodestring($input[form][title]) . "." . $f_info['extension'];
            if (move_uploaded_image($_FILES['img_file'], $DIR . $image_path . $file_name, null, 475)) {
                $input['form']['file_name'] = $file_name;
                $input['form']['file_type'] = $_FILES['img_file']['type'];
                $content.=my_msg_to_str('', '', 'Изображение успешно изменено.');
            } else {
                $content.=my_msg_to_str('error', '', 'Ошибка копирования файла !');
            }
        }
    }
    $query = "update {$TABLE} set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input['edit_image']) || ($input['add_image'])) {
    if ($input['edit_image']) {
        $query = "select * from {$TABLE} where id='{$input['id']}'";
        $result = my_query($query);
        $tags = array_merge($tags, $result->fetch_array());
        $tags['type'] = "edited_image";
        $tags['form_title'] = "Редактирование";
    } else {
        $tags['type'] = "added_image";
        $tags['form_title'] = "Добавление";
    }
    $tags['descr'] = "<textarea name=form[descr] rows=15 cols=100 maxlength=64000>{$tags['descr']}</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title('slider_images_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT * from {$TABLE} order by pos,title asc";
$result = my_query($query, true);
$content.=get_tpl_by_title("slider_images_edit_table", $tags, $result);



echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);