<?php

$tags[Header] = "Каталог";
include "../include/common.php";

if (isset($input["part_id"])) {
    $_SESSION["PART_ID"] = $input["part_id"];
}

list($part_title) = my_select_row("select title from cat_part where id='{$_SESSION["PART_ID"]}'", 1);
$tags[Header].=" -> $part_title";

$IMG_PATH = $DIR . $settings[catalog_item_img_path];
$IMG_URL = $BASE_HREF . $settings[catalog_item_img_path];

function show_img($tmp, $row) {
    global $IMG_PATH, $IMG_URL;
    if (is_file($IMG_PATH . $row[fname])) {
        return "<a href={$IMG_URL}$row[fname]><img src={$IMG_URL}$row[fname] border=0 width=150></a>";
    } else {
        return "Отсутствует";
    }
}

if ($input["del_image"]) {
    list($fname) = my_select_row("select fname from cat_item_image where id=" . $input["id"], 1);
    $result = @unlink($IMG_PATH . $fname);
    if (!$result) {
        echo "Error delete file !";
    } else {
        $query = "delete from cat_item_image where id=" . $input["id"];
        echo (my_query($query, $conn, 1) ? "OK" : mysql_error() );
    }
    exit;
}

if ($input["default_img"]) {
    $query = "update cat_item set default_img='" . $input["default_img"] . "' where id=" . $input["item_id"];
    echo (my_query($query, $conn, 1) ? "OK" : mysql_error() );
    exit;
}

if ($input["del"]) {
    $query = "select * from cat_item_image where item_id=" . $input["id"];
    $result = my_query($query, $conn);
    while ($row = $result->fetch_array()) {
        if (!unlink($IMG_PATH . $row[fname]))print_err("Ошибка удаления файла");
    }
    $query = "delete from cat_item_image where item_id=" . $input["id"];
    my_query($query, $conn);
    $query = "delete from cat_item where id=" . $input["id"];
    my_query($query, $conn);
}
if ($input["add_image"]) {
    if ($_FILES["img_file"]["size"]) {
        $query = "insert into cat_item_image(item_id,descr) values('{$input["id"]}','{$input["descr"]}')";
        my_query($query, $conn);
        $image_id = $mysqli->insert_id;
        $f_info = pathinfo($_FILES["img_file"]["name"]);
        $img = $input["id"] . "_" . $image_id . "." . $f_info["extension"];
        if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings["catalog_item_img_max_width"])) {
            $query = "update cat_item_image set fname='$img' where id='$image_id'";
            my_query($query, $conn);
            $query = "select id from cat_item_image where item_id='{$input["id"]}'";
            $result = my_query($query, $conn);
            if ($result->num_rows==1) {
                $query = "update cat_item set default_img='$image_id' where id='{$input["id"]}'";
                my_query($query, $conn);
            }
        } else {
            $query = "delete from cat_item_image where id='$image_id'";
            my_query($query, $conn);
            print_err("Ошибка копирования файла !");
        }
        $input["edit"]=1;
        $input["id"]=$input["id"];
    }
}

if ($input["added"]) {
    if(!strlen($input["form"]["num"])){
        list($input["form"]["num"])=my_select_row("select max(num) from cat_item where part_id='{$_SESSION["PART_ID"]}'",0);
        $input["form"]["num"]++;
    }
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $num_rows=my_select_row("select id from cat_item where seo_alias='{$input[form][seo_alias]}'",1);
    if($num_rows>0){
        $seo_alias_duplicate=1;
    }
    $input[form][part_id] = $_SESSION["PART_ID"];
    if (!isset($input[form][special_offer]))$input[form][special_offer] = 0;
    $query = "insert into cat_item " . db_insert_fields($input[form]);
    my_query($query, $conn);
    $insert_id=$mysqli->insert_id;
    if($seo_alias_duplicate){
        $input[form][seo_alias].='_'.$insert_id;
        my_query("update cat_item set seo_alias='{$input[form][seo_alias]}' where id='{$insert_id}'", $conn);
    }
    print_ok("Наменование добавлено");
    $input["id"]=$insert_id;
    $input["edit"]=1;
}

if ($input["edited"]) {
    $input[form][part_id] = $_SESSION["PART_ID"];
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
//    $num_rows=my_select_row("select id from cat_item where seo_alias='{$input[form][seo_alias]}'",0);
//    if($num_rows>1){
//        $input[form][seo_alias].='_'.$input[id];
//    }
    if (!isset($input[form][special_offer]))$input[form][special_offer] = 0;
    $query = "update cat_item set " . db_update_fields($input[form]) . " where id='$input[id]'";
    my_query($query, $conn);
    print_ok("Изменения сохранены");
//    $input["id"]=$input[id];
    $input["edit"]=1;
}

function get_image_list($item_id) {
    global $conn, $IMG_URL, $server;
    $query = "select cat_item_image.*,default_img,cat_item.id as item_id from cat_item_image left join cat_item on (cat_item.id=item_id) where item_id='$item_id'";
    $result = my_query($query, $conn);
//	if(!$result->num_rows)return iconv('windows-1251', 'UTF-8',"Изображения отсутствуют");
    $content = "<table width=550 border=0 cellspacing=1 cellpadding=1 class=admin align=center>
	<tr class=header align=center>
		<td width=15%>По умолчанию</td>
		<td width=45%>Изображение</td>
		<td width=25%>Описание</td>
		<td width=15%>Удалить</td>
	</tr>";
    while ($row = $result->fetch_array()) {
        $content.="<tr class=content valign=middle align=left>
		<td align=center><input type=radio name=ch_default class=default_img image_id=$row[id]" . ($row[default_img] == $row[id] ? " checked" : "") . "></td>
		<td align=center><img src={$IMG_URL}$row[fname] border=0 width=150></td>
		<td><span id=descr_$row[id]>$row[descr]</span></td>
		<td align=center><a href=# class=del_button image_id=$row[id] alt=\"Удалить\"><img src=\"../images/del.gif\"  border=0></a></td>
		</tr>";
    }
    $content.="</table>";
//    $content = iconv('windows-1251', 'UTF-8', $content);
    return $content;
}

if ($input["get_image_list"]) {
    echo get_image_list($input["item_id"]);
    exit;
}

if (($input["edit"]) || ($input["add"])) {
    if ($input["edit"]) {
        $query = "select * from cat_item where id='{$input["id"]}'";
        $result = my_query($query, $conn);
        $tags=array_merge($tags,$result->fetch_array());
        $tags[form_title] = "Редактирование";
        $tags[type] = "edited";
        $tags[Header] = "Редактирование товара";
        $tags[descr]=  strip_tags($tags[descr]);
    } else {
        $tags[form_title] = "Добавление";
        $tags[type] = "added";
        $tags[Header] = "Добавление товара";
        $tags[price] = "";
    }
    $row_part = my_select_row("select * from cat_part where id=" . $_SESSION["PART_ID"], 1);
    if ($tags[special_offer])$tags[special_offer] = " checked";

    $tags[price_inputs] = "
	<tr class=content align=left><td>$row_part[price1_title]</td><td><input type=edit maxlength=45 size=64 name=form[price] value=\"$tags[price]\"></td></tr>";
    if ($row_part[price_cnt] >= 2)$tags[price_inputs].="<tr class=content align=left><td>$row_part[price2_title]</td><td><input type=edit maxlength=45 size=64 name=form[price2] value=\"$tags[price2]\"></td></tr>";
    if ($row_part[price_cnt] >= 3)$tags[price_inputs].="<tr class=content align=left><td>$row_part[price3_title]</td><td><input type=edit maxlength=45 size=64 name=form[price3] value=\"$tags[price3]\"></td></tr>";

//	$tags[images]=get_image_list($input["id"]);

    $tags['INCLUDE_HEAD'] = $JQUERY_INC . $JQUERY_FORM_INC . $EDITOR_MINI_INC;
    $content = get_tpl_by_title("cat_item_form", $tags);
    if($input["edit"]){
        $content .= get_tpl_by_title("cat_item_image_form", $tags);
    }
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit;
};

$query = "SELECT cat_item.*,fname from cat_item
left join cat_item_image on (cat_item_image.id=default_img)
where part_id='{$_SESSION["PART_ID"]}' order by num,cat_item.id,title asc";
$result = my_query($query, $conn);

$content = get_tpl_by_title("cat_item_table", $tags, $result);
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>