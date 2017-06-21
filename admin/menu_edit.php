<?php
$tags[Header] = "Пункты меню";
include "../include/common.php";

$tags['INCLUDE_HEAD']=$JQUERY_INC;

if ($input["view_menu"]) {
    $_SESSION["view_menu"] = $input["id"];
}

if ($input["view_list"]) {
    unset($_SESSION["view_menu"]);
}

if ($_SESSION["view_menu"]) {
    list($list_title) = my_select_row("select title from menu_list where id='" . $_SESSION["view_menu"] . "'", 1);
    $tags[Header].=" -> $list_title";
}

if ($input["del_menu_item"]) {
    $query = "delete from menu_item where id='$input[id]'";
    my_query($query, $conn);
}

function get_item_title($target_type,$traget_id){
    global $conn;
    list($title) = my_select_row("select title from ".$target_type." where id='{$traget_id}'", true);
    return $title;
}

if ($input["added_menu_item"]) {
    if (!isset($input[form][active]))$input[form][active] = 0;
    $input[form][menu_id] = $_SESSION["view_menu"];
    if(!strlen($input[form][title]))$input[form][title]=get_item_title($input[form][target_type],$input[form][target_id]);
    $query = "insert into menu_item " . db_insert_fields($input[form]);
    my_query($query, $conn);
}

if ($input["edited_menu_item"]) {
    if (!isset($input[form][active]))$input[form][active] = 0;
    if(!strlen($input[form][title]))$input[form][title]=get_item_title($input[form][target_type],$input[form][target_id]);
    $query = "update menu_item set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn);
}

if($input["get_target_select"]){
    $query = "select target_id,href from menu_item where id='{$input["item_id"]}'";
    $result = my_query($query, $conn,1);
    list($target_id,$href) = $result->fetch_array();
    switch ($input["target_type"]){
        case "":
            $output="<td>Прямая ссылка:</td><td><input type=edit maxlength=255 size=64 name=form[href] value=\"$href\" class=\"form-control\"></td>";
            break;
        case "article":
            $query = "select * from article_item order by title";
            $result = my_query($query, $conn,1);
            $output = "<td>Статья:</td><td><select name=form[target_id] class=\"form-control\">";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "article_list":
            $query = "select * from article_list order by title";
            $result = my_query($query, $conn,1);
            $output = "<td>Раздел статей:</td><td><select name=form[target_id] class=\"form-control\">";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "media_list":
            $query = "select * from media_list order by title";
            $result = my_query($query, $conn,1);
            $output = "<td>Раздел файлов:</td><td><select name=form[target_id] class=\"form-control\">";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "cat_part":
            $query = "select * from cat_part where prev_id=0 order by title";
            $result = my_query($query, $conn,1);
            $output = "<td>Раздел каталога:</td><td><select name=form[target_id] class=\"form-control\">";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
        case "gallery_list":
            $query = "select * from gallery_list order by title";
            $result = my_query($query, $conn,1);
            $output = "<td>Раздел галереи:</td><td><select name=form[target_id] class=\"form-control\">";
            while ($row = $result->fetch_array()) {
                $output.="<option value=$row[id]" . ($row[id] == $target_id ? " selected" : "") . ">$row[title]</option>";
            }
            $output.="</select></td>";
            break;
    }
    echo $output;
    exit;
}

if (($input["add_menu_item"]) || ($input["edit_menu_item"])) {
    if ($input["edit_menu_item"]) {
	$query = "select * from menu_item where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags[type] = "edited_menu_item";
	$tags[form_title] = "Редактирование";
    } else {
	$tags[type] = "added_menu_item";
	$tags[form_title] = "Добавление";
	$tags[css_class] = "default";
	$tags[active] = " checked";
    }
    if ($tags[active])$tags[active] = " checked";
    $query = "select * from users_flags";
    $result = my_query($query, $conn);
    while ($row = $result->fetch_array()) {
	$tags[flag_select].="<option value=$row[value]" . ($row[value] == $tags[flag] ? " selected" : "") . ">$row[title]</option>";
    }
    $query = "select * from menu_list where id<>'$input[id]'";
    $result = my_query($query, $conn);
    $tags['submenu_select'] = "<select name=form[submenu_id] class=\"form-control\"><option value=0>-</option>";
    while ($row = $result->fetch_array()) {
	$tags[submenu_select].="<option value=$row[id]" . ($row[id] == $tags[submenu_id] ? " selected" : "") . ">$row[title]</option>";
    }
    $tags['submenu_select'].="</select>";
    $tags["target_type_select"]="
        <select name=\"form[target_type]\" id=\"target_type\" class=\"form-control\">
            <option " . ($tags["target_type"] == "" ? "selected" : "") . " value=\"\">Ссылка</option>
            <option " . ($tags["target_type"] == "article_list" ? "selected" : "") . " value=\"article_list\">Раздел статей</option>
            <option " . ($tags["target_type"] == "article" ? "selected" : "") . " value=\"article\">Статья</option>
            <option " . ($tags["target_type"] == "media_list" ? "selected" : "") . " value=\"media_list\">Раздел файлов</option>
            <option " . ($tags["target_type"] == "cat_part" ? "selected" : "") . " value=\"cat_part\">Раздел каталога</option>
            <option " . ($tags["target_type"] == "gallery_list" ? "selected" : "") . " value=\"gallery_list\">Раздел галереи</option>
        </select>
    ";
    $content.=get_tpl_by_title("menu_item_edit_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

if ($_SESSION["view_menu"]) {
    $query = "SELECT * from menu_item where menu_id='" . $_SESSION["view_menu"] . "' order by position asc";
    $result = my_query($query, $conn);
    $content.=get_tpl_by_title("menu_item_edit_table", $tags, $result);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
}

if ($input["del_menu"]) {
    $query = "select id from menu_item where menu_id='$input[id]'";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
	$content.=my_msg_to_str("error","","Этот раздел не пустой !");
    } else {
	$query = "delete from menu_list where id='$input[id]'";
	my_query($query, $conn);
    }
}

if ($input["added_menu"]) {
    if (!isset($input[form][root]))$input[form][root] = 0;
    if (!isset($input[form][top_menu]))$input[form][top_menu] = 0;
    if (!isset($input[form][bottom_menu]))$input[form][bottom_menu] = 0;
    $query = "insert into menu_list " . db_insert_fields($input[form]);
    my_query($query, $conn);
}


if ($input["edited_menu"]) {
    if (!isset($input[form][root]))$input[form][root] = 0;
    if (!isset($input[form][top_menu]))$input[form][top_menu] = 0;
    if (!isset($input[form][bottom_menu]))$input[form][bottom_menu] = 0;
    $query = "update menu_list set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn);
}

if (($input["add_menu"]) || ($input["edit_menu"])) {
    if ($input["edit_menu"]) {
	$query = "select * from menu_list where id='$input[id]'";
	$result = my_query($query, $conn);
	$tags = array_merge($tags, $result->fetch_array());
	$tags['type'] = "edited_menu";
	$tags['form_title'] = "Редактирование";
    } else {
	$tags['type'] = "added_menu";
	$tags['form_title'] = "Добавление";
	$tags['css_class'] = "default";
    }
    if ($tags['root'])$tags['root'] = " checked";
    if ($tags['top_menu'])$tags['top_menu'] = " checked";
    if ($tags['bottom_menu'])$tags['bottom_menu'] = " checked";
    $content.=get_tpl_by_title('menu_edit_form', $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, '', $content);
    exit();
}

$query = "select * from menu_list order by root desc,title asc";
$result = my_query($query, $conn, true);
$content.=get_tpl_by_title('menu_edit_table', $tags, $result);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
?>
