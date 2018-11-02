<?php

$tags['Header'] = 'Галерея';
include '../include/common.php';

$IMG_PATH = $DIR.$settings['gallery_list_img_path'];

if ($input['view_gallery']) {
    $_SESSION['view_gallery'] = $input['id'];
}

if ($input['list_gallery']) {
    $_SESSION['view_gallery'] = "";
}

if ($input['active']) {
    $query = "update gallery_list set active='" . $input['active'] . "' where id='" . $input['id'] . "'";
    if (my_query($query, true)) {
        print $input['active'];
    } else {
        print mysql_error();
    }
    exit;
}

if ($_SESSION['view_gallery']) {
    list($list_title) = my_select_row("select title from gallery_list where id='" . $_SESSION["view_gallery"] . "'", 1);
    $tags['Header'].=" -> $list_title";
}

<<<<<<< HEAD
if ($input['default_image_id']) {list($gallery_id) = my_select_row("select gallery_id from gallery_images where id='" . $_POST["default_image_id"] . "'", 1);
=======
if ($input['default_image_id']) {
    list($gallery_id) = my_select_row("select gallery_id from gallery_images where id='" . $_POST["default_image_id"] . "'", 1);
>>>>>>> current
    $query = "update gallery_list set default_image_id='" . $_POST["default_image_id"] . "' where id='{$gallery_id}'";
    echo (my_query($query, true) ? "OK" : mysql_error() );
    exit;
}

function is_default_image($tmp, $row) {
    
    list($default_image_id) = my_select_row("select default_image_id from gallery_list where id='{$row["gallery_id"]}'", true);
    if($default_image_id===$row["id"]) return " checked";
    return "";
}

function show_img($tmp, $row) {
    global $DIR, $settings;
    if (is_file($DIR . $settings["gallery_upload_path"] . $row['file_name'])) {
    	return "<a href=../modules/gallery/image.php?id={$row['id']}><img src=\"../modules/gallery/image.php?preview=1&id={$row['id']}\" border=0></a>";
    } else {
	    return "Отсутствует";
    }
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}


if ($input['add_multiple']){
    if ($_FILES['files']) {
        $file_ary = reArrayFiles($_FILES['files']);

        foreach ($file_ary as $file) {
            $input['form'][date_add] = "now()";
            $input['form'][gallery_id] = $_SESSION['view_gallery'];
            $query = "insert into gallery_images " . db_insert_fields($input['form']);
            my_query($query);    
            if ($file["size"] > 100) {
                if (!in_array($file["type"], $validImageTypes)) {
                    $content.=my_msg_to_str('error','','Неверный тип файла !');
                } else {
                    $image_id = $mysqli->insert_id;
                    $f_info = pathinfo($file['name']);
                    $file_name = $f_info['basename'];
                    $file_name = encodestring($file_name) . "." . $f_info["extension"];
                    if (move_uploaded_image($file, $DIR . $settings["gallery_upload_path"] . $file_name, 1024)) {
                        $query = "update gallery_images set file_name='{$file_name}',file_type='{$file['type']}' where id='{$image_id}'";
                        my_query($query);
                        $content.=my_msg_to_str('', '', 'Фотография успешно добавлена.');
                    } else {
                        $content.=my_msg_to_str('error','','Ошибка копирования файла !');
                    }
                }
            }
        }
    }    
}

if ($input['del_image']) {
    list($img_old) = my_select_row("select file_name from gallery_images where id='{$input['id']}'");
    if (is_file($DIR . $settings['gallery_upload_path'] . $img_old)) {
	if (!unlink($DIR . $settings['gallery_upload_path'] . $img_old)){ 
            $content.=my_msg_to_str('error','','Ошибка удаления файла');
        }    
    }
    $query = "delete from gallery_images where id='{$input['id']}'";
    my_query($query);
    $content.=my_msg_to_str('', "", "Фотография успешно удалена.");
}

// print_array($_FILES["img_file"]);

if ($input['added_image']) {
    $input['form']['date_add'] = 'now()';
    $input['form']['gallery_id'] = $_SESSION['view_gallery'];
    $query = "insert into gallery_images " . db_insert_fields($input['form']);
    my_query($query);    
    if ($_FILES['img_file']["size"] > 100) {
	if (!in_array($_FILES['img_file']['type'], $validImageTypes)) {
	    $content.=my_msg_to_str('error','','Неверный тип файла !');
	} else {
            $image_id=$mysqli->insert_id;
	    $f_info = pathinfo($_FILES['img_file']['name']);
	    $file_name = encodestring($input['form'][title]) . "." . $f_info['extension'];
	    if (move_uploaded_image($_FILES["img_file"], $DIR . $settings['gallery_upload_path'] . $file_name, 1024)) {
		$query = "update gallery_images set file_name='{$file_name}',file_type='" . $_FILES['img_file']['type'] . "' where id='{$image_id}'";
		my_query($query);
		$content.=my_msg_to_str('', '', 'Фотография успешно добавлена.');
	    } else {
		$content.=my_msg_to_str('error','','Ошибка копирования файла !');
	    }
	}
    }
}

if ($input['edited_image']) {
    if ($_FILES['img_file']['size'] > 100) {
	if (!in_array($_FILES['img_file']['type'], $validImageTypes)) {
	    $content.=my_msg_to_str('error','','Неверный тип файла !');
	} else {
	    list($img_old) = my_select_row("select file_name from gallery_images where id='{$input['id']}'");
	    if (is_file($DIR . $settings['gallery_upload_path'] . $img_old)) {
		if (!unlink($DIR . $settings['gallery_upload_path'] . $img_old)
		    )$content.=my_msg_to_str('error','','Ошибка удаления файла');
	    }
	    $f_info = pathinfo($_FILES['img_file']['name']);
	    $file_name = encodestring($input['form'][title]) . "." . $f_info['extension'];
	    if (move_uploaded_image($_FILES['img_file'], $DIR . $settings['gallery_upload_path'] . $file_name, 1024)) {
		$input['form']['file_name'] = $file_name;
		$input['form']['file_type'] = $_FILES['img_file']['type'];
		$content.=my_msg_to_str('', '', 'Фотография успешно изменена.');
	    } else {
		$content.=my_msg_to_str('error','','Ошибка копирования файла !');
	    }
	}
    }
    $query = "update gallery_images set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
}

if (($input['edit_image']) || ($input['add_image'])) {
    if ($input['edit_image']) {
	$query = "select * from gallery_images where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = "edited_image";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['type'] = "added_image";
	$tags['form_title'] = "Добавление";
    }
    $tags['descr'] = "<textarea name=form[descr] class=\"form-control\" rows=15 cols=100 maxlength=64000>$tags[descr]</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    $content.=get_tpl_by_title('gallery_image_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($_SESSION['view_gallery']) {
    $query = "SELECT * from gallery_images where gallery_id='" . $_SESSION['view_gallery'] . "' order by date_add asc";
    $result = my_query($query, true);
    $content.=get_tpl_by_title('gallery_image_edit_table', $tags, $result);
    $tags['INCLUDE_HEAD']=$JQUERY_INC;
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

if ($input["del_gallery"]) {
    $query = "select id from gallery_images where gallery_id='{$input['id']}'";
    $result = my_query($query);
    if ($result->num_rows) {
	$content.=my_msg_to_str("error","","Этот раздел не пустой !");
    } else {
        list($img) = my_select_row("select image_name from gallery_list where id=" . $_GET["id"]);
        if (is_file($IMG_PATH . $img)) {
            if (!unlink($IMG_PATH . $img))my_msg_to_str("", "", "Ошибка удаления файла");
        }
	$query = "delete from gallery_list where id='{$input['id']}'";
	my_query($query);
	$content.=my_msg_to_str("", "", "Галерея успешно удалена.");
    }
}

if ($_GET["del_gallery_list_image"]) {
    list($img) = my_select_row("select image_name from gallery_list where id=" . $_GET["id"]);
    if (is_file($IMG_PATH . $img)) {
        if (!unlink($IMG_PATH . $img))my_msg_to_str("", "", "Ошибка удаления файла");
    }
    $query = "update gallery_list set image_name='-' where id=" . $_GET["id"];
    my_query($query);
    $_GET["edit"] = 1;
}


if ($input["added_gallery"]) {
    if (!strlen($input['form'][seo_alias]))$input['form'][seo_alias] = encodestring($input['form'][title]);
    $input['form'][date_add] = "now()";
    $query = "insert into gallery_list " . db_insert_fields($input['form']);
    my_query($query);
    if ($_FILES["img_file"]["size"]) {
        $part_id = $mysqli->insert_id;
        $f_info = pathinfo($_FILES["img_file"]["name"]);
        $img = encodestring($input["form"]["title"]) . "." . $f_info["extension"];
        if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings['gallery_list_img_max_width'])) {
            $query = "update gallery_list set image_name='$img' where id=$part_id";
            my_query($query);
        } else {
            my_msg_to_str("error", "", "Ошибка копирования файла !");
        }
    }
    $content.=my_msg_to_str("", "", "Галерея успешно добавлена.");
}

if ($input["edited_gallery"]) {
    if (!strlen($input['form']['seo_alias']))$input['form']['seo_alias'] = encodestring($input['form']['title']);
    $query = "update gallery_list set " . db_update_fields($input['form']) . " where id='{$input['id']}'";
    my_query($query);
    if ($_FILES["img_file"]["size"] > 100) {
        list($img) = my_select_row("select image_name from gallery_list where id=" . $input["id"]);
        if (is_file($IMG_PATH . $img)) {
            if (!unlink($IMG_PATH . $img))
                print_error("Ошибка удаления файла");
        }
        $f_info = pathinfo($_FILES["img_file"]["name"]);
        $img = encodestring($input["form"]["title"]) . "." . $f_info["extension"];
        echo $IMG_PATH . $img;
        if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings['gallery_list_img_max_width'])) {
            $query = "update gallery_list set image_name='$img' where id=" . $input["id"];
            my_query($query);
        } else {
            my_msg_to_str("error", "", "Ошибка копирования файла !");
        }
    }
    $content.=my_msg_to_str('', '', 'Галерея успешно изменена.');
}

if (($input['edit_gallery']) || ($input['add_gallery'])) {
    if ($_GET["edit_gallery"]) {
	$query = "select * from gallery_list where id='{$input['id']}'";
	$result = my_query($query);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = 'edited_gallery';
	$tags['form_title'] = 'Редактирование';
    } else {
	$tags['type'] = 'added_gallery';
	$tags['form_title'] = 'Добавление';
    }
    $tags['descr'] = "<textarea class=\"form-control\" name=form[descr] rows=15 cols=100 maxlength=64000>{$tags['descr']}</textarea>";
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    
    $tags['img_tag'] = (is_file($IMG_PATH . $tags['image_name']) ? "<img src=../{$settings['gallery_list_img_path']}{$tags['image_name']} class=margin><br>" : "[ Отсутствует ]<br>");
    $tags['del_button'] = (is_file($IMG_PATH . $tags['image_name']) ? "<a href=" . $server['PHP_SELF'] . "?del_gallery_list_image=1&id={$tags['id']}>Удалить</a><br>" : "");

    $content.=get_tpl_by_title('gallery_list_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

$query = "SELECT gallery_list.*,count(gallery_images.id) as images
from gallery_list 
left join gallery_images on (gallery_images.gallery_id=gallery_list.id) 
group by gallery_list.id order by gallery_list.date_add desc";
$result = my_query($query, true);

$tags['INCLUDE_HEAD']=$JQUERY_INC;

$content.=get_tpl_by_title("gallery_list_edit_table", $tags, $result);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
