<?php

namespace modules\media;

use Classes\BaseController;
use Classes\App;
use Classes\Pagination;

/**
 * Description of Controller
 *
 * @author User
 */
class Controller extends BaseController {
    
    public function actionPartList()
    {
        $this->title = 'Файлы';
        $this->breadcrumbs[] = ['title'=>$this->title];        
        $query = "SELECT 
                media_list.*,count(files.id) AS files,max(files.date_add) AS last_files_date_add
            FROM media_list
            LEFT JOIN media_files AS files ON (files.list_id=media_list.id)
            GROUP BY media_list.id ORDER BY last_files_date_add DESC,media_list.date_add DESC";
        $result = my_query($query);
        if ($result->num_rows) {
            return App::$template->parse('media_part_list', [], $result);
        } else {
            return App::$message->get('part_empty');
        }
    }
    
    public function actionFilesList($alias, $page = 1)
    {
        $view_media = get_id_by_alias('media_list', $alias, true);            
        list($media_title, $media_seo_alias, $media_descr) = App::$db->select_row("SELECT title, seo_alias, descr from media_list where id='{$view_media}'");
        $tags['list_descr'] = $media_descr;
        
        $this->title = $media_title;
        $this->breadcrumbs[] = ['title' => 'Файлы', 'url'=>'media/'];        
        $this->breadcrumbs[] = ['title' => $media_title];

        list($total) =  App::$db->select_row("SELECT count(id) from media_files where list_id='{$view_media}'");
        $pager = new Pagination($total, $page, App::$settings['media_files_per_page']);
        $tags['pager'] = $pager;
        $tags['media_list_href'] = 'media/'.$media_seo_alias.'/';
        
        $tags['self'] = $this;

        $query = "SELECT * from media_files where list_id='" . $view_media . "' order by num asc, id asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);
        if (!$result->num_rows) {
            $content = App::$message->get('list_empty', [], '');
        } else {
            $tags['this'] = $this;
            $this->tags['INCLUDE_HEAD'].='<link rel="stylesheet" href="'.App::$SUBDIR.'modules/media/player/mediaelementplayer.min.css" />';
            $this->tags['INCLUDE_JS'].='<script src="'.App::$SUBDIR.'modules/media/player/mediaelement-and-player.min.js"></script>';
            $this->tags['INCLUDE_JS'].="<script>$('audio,video').mediaelementplayer();</script>";
            $content = App::$template->parse('media_files_list', $tags, $result);
        }
        return $content;
    }
    
    public function show_size($row) {
        global $player_num, $player_show;
        $file_name = App::$settings['media_upload_path'] . $row['file_name'];
        $content='';

        $f_info = pathinfo($file_name);
        $href= App::$SUBDIR . "modules/media/download.php?media_file_id={$row['id']}&download_file_name=" . urlencode($row['title']) . "." . $f_info["extension"];

        if (is_file(App::$DIR . $file_name)) {
            $content = '<a href="'.$href.'" class="btn btn-default"> <b>Скачать файл</b> ( размер: ' . convert_bytes(filesize(App::$DIR . $file_name)) . ', загрузок '.$row['download_count'].' )</a>';
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
        if (stristr($file_name, ".mp4")) {
            $player_show = 1;
            $player_tag = 'video';
            $player_type='video/mp4';
            $player_num++;
            $player_height = 240;
            $player_fullscreen = 'true';
        }
        if ($player_show) {
            $content .= '
                <div class="player-tag">
                    <'.$player_tag.' id="player'.$player_num.'" src="'.App::$SUBDIR.$file_name.'" type="'.$player_type.'" controls="controls" width="320" height="'.$player_height.'"></'.$player_tag.'>		
                </div>
            ';
        }
        return $content;
    }

    
    
}




