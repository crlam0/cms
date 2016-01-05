<?php

$tags[Header] = "Каталог";
include "../include/common.php";

/*
$query = "select * from cat_part";
$result = my_query($query, $conn, 1);
while ($row = $result->fetch_array()) {
    $query="update cat_part set seo_alias='".encodestring($row["title"])."' where id='{$row["id"]}'";
    my_query($query, $conn, 1);
}
*/

$IMG_PATH = $DIR . $settings[catalog_part_img_path];
$IMG_URL = $BASE_HREF . $settings[catalog_part_img_path];

if ($_GET["del"]) {
    $query = "select count(id) as cnt from cat_part where prev_id=" . $_GET["id"] . " having cnt>0
	union select count(id) as cnt from cat_item where part_id=" . $_GET["id"] . " having cnt>0";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
	print_err("Этот раздел не пустой !");
    } else {
	list($img) = my_select_row("select img from cat_part where id=" . $_GET["id"]);
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img)
		)print_err("Ошибка удаления файла");
	}
	$query = "delete from cat_part where id=" . $_GET["id"];
	my_query($query, $conn);
    }
}

if ($_GET["del_img"]) {
    list($img) = my_select_row("select img from cat_part where id=" . $_GET["id"]);
    if (is_file($IMG_PATH . $img)) {
	if (!unlink($IMG_PATH . $img)
	    )print_err("Ошибка удаления файла");
    }
    $query = "update cat_part set img='-' where id=" . $_GET["id"];
    my_query($query, $conn);
    $_GET["edit"] = 1;
}

if ($_POST["added"]) {
    if(!strlen($input["form"]["num"])){
        list($input["form"]["num"])=my_select_row("select max(num) from cat_part where prev_id='{$input["form"]["prev_id"]}'",0);
        $input["form"]["num"]++;
    }
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "insert into cat_part " . db_insert_fields($input[form]);
    my_query($query, $conn, 0);
    print_ok("Раздел успешно добавлен.");
    if ($_FILES["img_file"]["size"]) {
	$part_id = $mysqli->insert_id;
	$f_info = pathinfo($_FILES["img_file"]["name"]);
	$img = $part_id . "." . $f_info["extension"];
//		if(move_uploaded_file($_FILES["img_file"]["tmp_name"],$IMG_PATH.$img)){
	if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings["catalog_part_img_max_width"])) {
	    $query = "update cat_part set img='$img' where id=$part_id";
	    my_query($query, $conn);
	} else {
	    print_err("Ошибка копирования файла !");
	}
    }
}

if ($_POST["edited"]) {
    if (!strlen($input[form][seo_alias]))$input[form][seo_alias] = encodestring($input[form][title]);
    $query = "update cat_part set " . db_update_fields($input[form]) . " where id=" . $_POST["id"];
    my_query($query, $conn, 1);
    print_ok("Раздел успешно изменен.");
    if ($_FILES["img_file"]["size"] > 100) {
	list($img) = my_select_row("select img from cat_part where id=" . $_POST["id"]);
	if (is_file($IMG_PATH . $img)) {
	    if (!unlink($IMG_PATH . $img)
		)print_err("Ошибка удаления файла");
	}
	$f_info = pathinfo($_FILES["img_file"]["name"]);
	$img = $_POST["id"] . "." . $f_info["extension"];
//		if(move_uploaded_file($_FILES["img_file"]["tmp_name"],$IMG_PATH.$img)){
	if (move_uploaded_image($_FILES["img_file"], $IMG_PATH . $img, $settings["catalog_part_img_max_width"])) {
	    $query = "update cat_part set img='$img' where id=" . $_POST["id"];
	    my_query($query, $conn);
	} else {
	    print_err("Ошибка копирования файла !");
	}
    }
}

if (($_GET["edit"]) || ($_GET["adding"])) {
    if ($_GET["edit"]) {
	$query = "select * from cat_part where id=" . $_GET["id"];
	$result = my_query($query, $conn);
	$tags = $result->fetch_array();
	$tags[form_title] = "Редактирование";
	$tags[type] = "edited";
	$tags[Header] = "Редактирование раздела";
    } else {
	$tags[price_cnt] = 1;
	$tags[price1_title] = 'Цена, руб.';
	$tags[price2_title] = 'Цена 2, руб.';
	$tags[price3_title] = 'Цена 3, руб.';
	$tags[form_title] = "Добавление";
	$tags[type] = "added";
	$tags[Header] = "Добавление раздела";
    }
    $tags['INCLUDE_HEAD'] = $EDITOR_SIMPLE_INC;
    $query = "select * from cat_part where id<>'$tags[id]' order by prev_id,title asc";
    $result = my_query($query, $conn, 1);
    while ($row = $result->fetch_array()) {
	$tags[prev_id_select].="<option value=$row[id]" . ($tags[prev_id] == $row[id] ? " selected" : "") . ">$row[title]</option>";
    }

    $tags[img_tag] = (is_file($IMG_PATH . $tags[img]) ? "<img src=../$settings[catalog_part_img_path]$tags[img] class=margin><br>" : "[ Отсутствует ]<br>");
    $tags[del_button] = (is_file($IMG_PATH . $tags[img]) ? "<a href=" . $server["PHP_SELF"] . "?del_img=1&id=$tags[id]>Удалить</a><br>" : "");

    $content = get_tpl_by_title("cat_part_form", $tags);
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit;
};

function sub_part($prev_id, $deep) {
    global $conn, $IMG_PATH, $tags, $IMG_URL;
    $query = "SELECT * from cat_part where prev_id=$prev_id order by num,title+1 asc";
    $result = my_query($query, $conn);
    while ($row = $result->fetch_array()) {
	$spaces = "";
	for ($i = 0; $i < $deep; $i++
	    )$spaces.="&nbsp;&nbsp;&nbsp;&nbsp;";
	$tags[table_content].="
		<tr class=content align=left>
			<td>$spaces<a href=cat_item_edit.php?part_id=$row[id]>$row[num] $row[title]</a></td>
			<td>$row[seo_alias]</a></td>
			<td align=center>" . (is_file($IMG_PATH . $row[img]) ? "<img src=$IMG_URL$row[img] border=0>" : "&nbsp;") . "</td>
			<td width=16><a href=" . $server["PHP_SELF"] . "?edit=1&id=$row[id]><img src=\"../images/open.gif\" width=16 height=16 alt=\"Редактировать\" border=0></a></td>
			<td width=16><a href=" . $server["PHP_SELF"] . "?del=1&id=$row[id]><img src=\"../images/del.gif\" alt=\"Удалить\" border=0 onClick=\"return test()\"></a></td>
		</tr>
		";
	sub_part($row[id], $deep + 1);
    }
}

$tags[table_content] = "";
sub_part(0, 0);
$content = get_tpl_by_title("cat_part_table", $tags);
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>
