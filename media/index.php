<?php

$tags[Header] = "Файлы";
include "../include/common.php";

$tags[nav_str].="<a href=" . $_SERVER["PHP_SELF_DIR"] . " class=nav_next>$tags[Header]</a>";

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

$player_num=0;

function show_size($tmp, $row) {
    global $DIR, $settings, $SUBDIR, $player_num;
    $file_name = $settings["media_upload_path"] . $row['file_name'];
    $content='';
    
    $f_info = pathinfo($file_name);
    $href=dirname($_SERVER['PHP_SELF']) . "/download.php?media_file_id={$row['id']}&file_name=" . urlencode($file_name) . "&download_file_name=" . urlencode($row['title']) . "." . $f_info["extension"];
    
    if (is_file($DIR . $file_name)) {
        $content = "<a href=\"{$href}\"> Скачать файл ( размер: " . convert_bytes(filesize($DIR . $file_name)) . ", загрузок {$row['download_count']} )</a>";
    } else {
        $content = "Файл отсутствует";
    }
    if (stristr($file_name, ".mp3")) {
        $player_show = 1;
        $player_tag = 'audio';
        $player_type='audio/mp3';
        $player_num++;
        $player_height = 24;
        $player_fullscreen = 'false';
    }
    if ((stristr($file_name, ".flv")) || (stristr($file_name, ".avi")) || (stristr($file_name, ".mp4")) || (stristr($file_name, ".wmv"))) {
        $player_show = 1;
        $player_tag = 'video';
        $player_type='video/flv';
        $player_num++;
        $player_height = 240;
        $player_fullscreen = 'true';
    }
    if ($player_show) {
        $content .= '
		<br>
                <'.$player_tag.' id="player'.$player_num.'" src="'.$SUBDIR.$file_name.'" type="'.$player_type.'" controls="controls" width="320" height="'.$player_height.'"></'.$player_tag.'>		

        ';
    }
    return $content;
}

if ($_SESSION["view_files"]) {
    list($PAGES) = my_select_row("SELECT ceiling(count(id)/$settings[media_files_per_page]) from media_files where list_id=" . $_SESSION["view_files"], 1);
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
    $offset = $settings[media_files_per_page] * ($_SESSION["media_page"] - 1);
    $query = "SELECT * from media_files where list_id=" . $_SESSION["view_files"] . " order by date_add desc limit $offset,$settings[media_files_per_page]";
    $result = my_query($query, $conn, true);
    if (!$result->num_rows) {
        $content = my_msg_to_str("list_empty", $tags, "");
    } else {
        $content = get_tpl_by_title("media_files_table", $tags, $result);
    }
    if($player_num>0){
        $tags['INCLUDE_HEAD']=$JQUERY_INC . '<script src="'.$SUBDIR.'media/player/mediaelement-and-player.min.js"></script>
        <link rel="stylesheet" href="'.$SUBDIR.'media/player/mediaelementplayer.min.css" />    
        ';
        $content.="<script>$('audio,video').mediaelementplayer();</script>";
    }

    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT media_list.*,count(media_files.id) as files
from media_list 
left join media_files on (media_files.list_id=media_list.id) 
group by media_list.id order by media_list.date_add desc";
$result = my_query($query, $conn, 0);
if (!$result->num_rows) {
    $content = my_msg_to_str("$part_empty");
} else {
    $content = get_tpl_by_title("media_list_table", $tags, $result);
}
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);

