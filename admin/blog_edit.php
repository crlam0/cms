<?php

$tags[Header] = "Блог";
include "../include/common.php";

$IMG_PATH = $DIR.$settings['blog_img_path'];

if ($input["get_target_select"]) {
    $query = "select target_id,href from blog_posts where id='{$input["item_id"]}'";
    $result = my_query($query, null, true);
    list($target_id, $href) = $result->fetch_array();
    switch ($input["target_type"]) {
        case "link":
            $output = "<td>Прямая ссылка:</td><td><input type=edit maxlength=255 size=64 name=form[href] value=\"$href\"></td>";
            break;
        case "article":
            $query = "select * from article_item order by title";
            $result = my_query($query, null, true);
            $output = "<td>Статья:</td><td><select name=form[target_id]>";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "article_list":
            $query = "select * from article_list order by title";
            $result = my_query($query, null, true);
            $output = "<td>Раздел статей:</td><td><select name=form[target_id]>";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "media_list":
            $query = "select * from media_list order by title";
            $result = my_query($query, null, true);
            $output = "<td>Раздел файлов:</td><td><select name=form[target_id]>";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "cat_part":
            $query = "select * from cat_part where prev_id=0 order by title";
            $result = my_query($query, null, true);
            $output = "<td>Раздел каталога:</td><td><select name=form[target_id]>";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "gallery_list":
            $query = "select * from gallery_list order by title";
            $result = my_query($query, null, true);
            $output = "<td>Раздел галереи:</td><td><select name=form[target_id]>";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
    }
    echo $output;
    exit;
}

if ($input["active"]) {
    $query = "update blog_posts set active='" . $input["active"] . "' where id=" . $input["id"];
    if (my_query($query, $conn, true)) {
        echo $input["active"];
    } else {
        echo mysql_error();
    }
    exit;
}

if ($input["del_post"]) {
    list($img) = my_select_row("select image_name from blog_posts where id=" . $_GET["id"]);
    if (is_file($IMG_PATH . $img)) {
        if (!unlink($IMG_PATH . $img))print_error("Ошибка удаления файла");
    }
    $query = "delete from blog_posts where id=" . $input["id"];
    $result = my_query($query, $conn);
    $query = "delete from comments where target_type='blog' and target_id=" . $input["id"];
    $result = my_query($query, $conn);
    $list = 1;
    $content.=my_msg_to_str("", "", "Пост успешно удален.");
}

if ($_GET["del_img"]) {
    list($img) = my_select_row("select image_name from blog_posts where id=" . $_GET["id"]);
    if (is_file($IMG_PATH . $img)) {
        if (!unlink($IMG_PATH . $img))print_error("Ошибка удаления файла");
    }
    $query = "update blog_posts set image_name='-' where id=" . $_GET["id"];
    my_query($query, $conn);
    $_GET["edit"] = 1;
}

if ($input["added_post"]) {
    $input[form][date_add] = "now()";
    $input[form][uid] = $_SESSION["UID"];
    // $input[form][content] = $input["form"]["content"];
    $input[form][content] = replace_base_href($input[form][content], true);
    if (!strlen($input[form][seo_alias]))
        $input[form][seo_alias] = encodestring($input[form][title]);
    $query = "insert into blog_posts " . db_insert_fields($input[form]);
    my_query($query, null, true);
    if ($_FILES["img_file"]["size"]) {
        $f_info = pathinfo($_FILES["img_file"]["name"]);
        $img = encodestring($input["form"]["title"]) . "." . $f_info["extension"];
        if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings['blog_img_max_width'])) {
            $query = "update blog_posts set image_name='$img' where id=$part_id";
            my_query($query, $conn);
        } else {
            print_error("Ошибка копирования файла !");
        }
    }
    $content.=my_msg_to_str("", "", "Пост успешно добавлен.");
}

if ($input["revert"]) {
    unset($input["edited_post"]);
    $input["edit_post"] = 1;
}

if ($input["edited_post"]) {
    $input[form][date_add] = "now()";
    $input[form][content] = $input["form"]["content"];
    $input[form][content] = replace_base_href($input[form][content], true);
    if (!strlen($input[form][seo_alias]))
        $input[form][seo_alias] = encodestring($input[form][title]);
    $query = "update blog_posts set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, null, true);
    if ($_FILES["img_file"]["size"] > 100) {
        list($img) = my_select_row("select image_name from blog_posts where id=" . $input["id"]);
        if (is_file($IMG_PATH . $img)) {
            if (!unlink($IMG_PATH . $img))print_error("Ошибка удаления файла");
        }
        $f_info = pathinfo($_FILES["img_file"]["name"]);
        $img = encodestring($input["form"]["title"]) . "." . $f_info["extension"];
        echo $IMG_PATH . $img;
        if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings['blog_img_max_width'])) {
            $query = "update blog_posts set image_name='$img' where id=" . $input["id"];
            my_query($query, $conn);
        } else {
            print_error("Ошибка копирования файла !");
        }
    }
    $content.=my_msg_to_str("", "", "Пост успешно изменен.");
    if ($input["update"]) {
        $input["edit_post"] = 1;
    }
}

if (($input["edit_post"]) || ($input["add_post"])) {
    if ($input["edit_post"]) {
        $query = "select * from blog_posts where id='$input[id]'";
        $result = my_query($query, $conn);
        $tags = array_merge($tags, $result->fetch_array());
        $tags[type] = "edited_post";
        $tags[form_title] = "Редактирование";
        $tags[Header] = "Редактирование поста";
    } else {
        $tags[type] = "added_post";
        $tags[form_title] = "Добавление";
        $tags[Header] = "Добавление поста";
    }
    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $EDITOR_INC;
    $tags["content"] = replace_base_href($tags["content"], false);
    $tags["target_type_select"] = "
        <select name=\"form[target_type]\" id=\"target_type\" class=\"form-control\">
            <option " . ($tags["target_type"] == "" ? "selected" : "") . " value=\"\">-</option>
            <option " . ($tags["target_type"] == "link" ? "selected" : "") . " value=\"link\">Ссылка</option>
            <option " . ($tags["target_type"] == "article_list" ? "selected" : "") . " value=\"article_list\">Раздел статей</option>
            <option " . ($tags["target_type"] == "article" ? "selected" : "") . " value=\"article\">Статья</option>
            <option " . ($tags["target_type"] == "media_list" ? "selected" : "") . " value=\"media_list\">Раздел файлов</option>
            <option " . ($tags["target_type"] == "cat_part" ? "selected" : "") . " value=\"cat_part\">Раздел каталога</option>
            <option " . ($tags["target_type"] == "gallery_list" ? "selected" : "") . " value=\"gallery_list\">Раздел галереи</option>
        </select>
    ";
    $tags[img_tag] = (is_file($IMG_PATH . $tags['image_name']) ? "<img src=../{$settings['blog_img_path']}{$tags['image_name']} class=margin><br>" : "[ Отсутствует ]<br>");
    $tags[del_button] = (is_file($IMG_PATH . $tags['image_name']) ? "<a href=" . $server["PHP_SELF"] . "?del_img=1&id=$tags[id]>Удалить</a><br>" : "");

    $content.=get_tpl_by_title("blog_post_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT * from blog_posts order by id desc";
$result = my_query($query, null, true);

$tags['INCLUDE_HEAD'] = $JQUERY_INC;

$content.=get_tpl_by_title("blog_edit_table", $tags, $result);
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>
