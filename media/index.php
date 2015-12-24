<?php

$tags[Header] = "Файлы";
include "../include/common.php";

$tags[nav_str].="<a href=" . $server["PHP_SELF_DIR"] . " class=nav_next>$tags[Header]</a>";

if (isset($input["uri"])) {
    $params = explode("/", $input["uri"]);

    $query = "select id from media_list where seo_alias like '" . $params[0] . "'";
    $result = my_query($query, $conn);
    list($_SESSION["view_files"]) = $result->fetch_array();

    if (strlen($params[1])) {
        $_SESSION["media_page"] = $params[1];
    } else {
        $_SESSION["media_page"] = 1;
    }
}

if ($input["view_files"]) {
    $_SESSION["view_files"] = $input["id"];
}

if ($input["list_media"]) {
    $_SESSION["view_files"] = "";
    $_SESSION["media_page"] = 1;
}

if (!isset($_SESSION["media_page"]))
    $_SESSION["media_page"] = 1;

if (isset($input["page"])) {
    $_SESSION["media_page"] = $input["page"];
}

if(!is_array($input)){
    $_SESSION["view_files"] = "";
    $_SESSION["media_page"] = 1;
}

function show_size($tmp, $row) {
    global $DIR, $settings, $SUBDIR, $player_num;
    $file_name = $settings["media_upload_path"] . $row[file_name];
    if (is_file($DIR . $file_name)) {
        $str = "<a href=" . $SUBDIR . $file_name . "> Скачать ( " . convert_bytes(filesize($DIR . $file_name)) . " )</a>";
    } else {
        $str = "Отсутствует";
    }
    if (stristr($file_name, ".mp3")) {
        $player_show = 1;
        $player_num++;
        $player_height = 24;
        $player_fullscreen = 'false';
    }
    if ((stristr($file_name, ".flv")) || (stristr($file_name, ".avi")) || (stristr($file_name, ".mp4")) || (stristr($file_name, ".wmv"))) {
        $player_show = 1;
        $player_num++;
        $player_height = 270;
        $player_fullscreen = 'true';
    }
    if ($player_show) {
        $str .= "
		<br>
		<object id='player$player_num' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' name='player$player_num' width='480' height='$player_height'>
		<param name='movie' value='player.swf' />
		<param name='allowfullscreen' value='$player_fullscreen' />
		<param name='allowscriptaccess' value='always' />
		<param name='flashvars' value='file=$SUBDIR$file_name&autostart=false' />
		<embed
			type='application/x-shockwave-flash'
			id='player$player_num'
			name='player$player_num'
			src='player.swf'
			width='480'
			height='$player_height'
			allowscriptaccess='always'
			allowfullscreen='$player_fullscreen'
			flashvars='file=$SUBDIR$file_name&autostart=false'
		/>
		</object> ";
    }
    return $str;
}

if ($_SESSION["view_files"]) {
    list($PAGES) = my_select_row("SELECT ceiling(count(id)/$settings[media_file_per_page]) from media_file where list_id=" . $_SESSION["view_files"], 1);
    list($title) = my_select_row("select title from media_list where id=" . $_SESSION["view_files"], 1);
    $tags[Header] = $title;
    $tags[nav_str].="<span class=nav_next>$title</span>";

    if ($PAGES > 1) {
        $tags[pages_list] = "<center>";
        for ($i = 1; $i <= $PAGES; $i++) {
            if ($i == $_SESSION["media_page"]) {
                $tags[pages_list].= "[ <b>$i</b> ]&nbsp;";
            } else {
                $tags[pages_list].= "[ <a href=" . $SUBDIR . get_media_list_href($_SESSION["view_files"]) . "$i/>$i</a> ]&nbsp;";
            }
        }
        $tags[pages_list].="</center><br>";
    }
    $offset = $settings[media_file_per_page] * ($_SESSION["media_page"] - 1);
    $query = "SELECT * from media_file where list_id=" . $_SESSION["view_files"] . " order by date_add asc limit $offset,$settings[media_file_per_page]";
    $result = my_query($query, $conn, true);
    if (!$result->num_rows) {
        $content = my_msg_to_str("list_empty", $tags, "");
    } else {
        $content = get_tpl_by_title("media_file_table", $tags, $result);
    }
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT media_list.*,count(media_file.id) as files
from media_list 
left join media_file on (media_file.list_id=media_list.id) 
group by media_list.id order by media_list.date_add desc";
$result = my_query($query, $conn, 0);
if (!$result->num_rows) {
    $content = my_msg_to_str("$part_empty");
} else {
    $content = get_tpl_by_title("media_list_table", $tags, $result);
}
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>
