<?php
if(!isset($input)) {
    require '../../include/common.php';
}
$tags['Header'] = "Файлы";
$tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.'css/media.css" type="text/css" rel=stylesheet />'."\n";

add_nav_item('Файлы', 'media/');

$view_files = '';
$player_show = 0;

use Classes\Pagination;

if (isset($input['uri'])) {
    $params = explode("/", $input['uri']);

    /*
    $query = "select id from media_list where seo_alias like '" . $params[0] . "'";
    $result = my_query($query);
    list($view_files) = $result->fetch_array();
     * 
     */
    $view_files = get_id_by_alias('media_list', $params[0], true);
    if (isset($params[1])) {
        $media_page = (int)$params[1];
    } else {
        $media_page = 1;
    }
}

if (!isset($media_page)){
    $media_page = 1;
}

if(!$input->count()){
    $view_files = null;
    $media_page = 1;
}

$player_num=0;

function show_size($row) {
    global $DIR, $settings, $SUBDIR, $player_num, $player_show, $server;
    $file_name = $settings['media_upload_path'] . $row['file_name'];
    $content='';
    
    $f_info = pathinfo($file_name);
    $href=dirname($server['PHP_SELF']) . "/download.php?media_file_id={$row['id']}&file_name=" . urlencode($file_name) . "&download_file_name=" . urlencode($row['title']) . "." . $f_info["extension"];
    
    if (is_file($DIR . $file_name)) {
        $content = '<a href="'.$href.'" class="btn btn-primary"> <b>Скачать файл</b> ( размер: ' . convert_bytes(filesize($DIR . $file_name)) . ', загрузок '.$row['download_count'].' )</a>';
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

if ($view_files) {
    list($title,$list_descr) = my_select_row("select title,descr from media_list where id='" . $view_files . "'", true);
    $tags['Header'] = $title;
    add_nav_item($title);
    
    $tags['media_list_href'] = get_media_list_href($view_files);
    $tags['list_descr'] = $list_descr;
    
    list($total) = my_select_row("SELECT count(id) from media_files where list_id='" . $view_files . "'", true);
    $pager = new Pagination($total, $media_page, $settings['media_files_per_page']);
    $tags['pager'] = $pager;
    
    $query = "SELECT * from media_files where list_id=" . $view_files . " order by num asc, id asc limit {$pager->getOffset()},{$pager->getLimit()}";
    $result = my_query($query, true);
    if (!$result->num_rows) {
        $content = my_msg_to_str('list_empty', $tags, '');
    } else {
        $content = get_tpl_by_title("media_files_list", $tags, $result);
    }
    if($player_num>0){
        $tags['INCLUDE_HEAD'].='<script src="'.$SUBDIR.'modules/media/player/mediaelement-and-player.min.js"></script>
        <link rel="stylesheet" href="'.$SUBDIR.'modules/media/player/mediaelementplayer.min.css" />    
        ';
        $content.="<script>$('audio,video').mediaelementplayer();</script>";
    }    
    
    echo get_tpl_default($tags, '', $content);
    exit();
}

$query = "SELECT media_list.*,count(media_files.id) as files
from media_list 
left join media_files on (media_files.list_id=media_list.id) 
group by media_list.id order by media_list.date_add desc";
$result = my_query($query, true);
if (!$result->num_rows) {
    $content = my_msg_to_str('part_empty');
} else {
    $content = get_tpl_by_title('media_part_list', $tags, $result);
}

echo get_tpl_default($tags, '', $content);

