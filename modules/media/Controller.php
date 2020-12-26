<?php

namespace modules\media;

use classes\BaseController;
use classes\App;
use classes\Pagination;

/**
 * Description of Controller
 *
 * @author User
 */
class Controller extends BaseController 
{
    
    public function actionPartList(): string
    {
        $this->title = 'Файлы';
        $this->breadcrumbs[] = ['title'=>$this->title];        
        $query = "SELECT 
                media_list.*,count(files.id) AS files,max(files.date_add) AS last_files_date_add
            FROM media_list
            LEFT JOIN media_files AS files ON (files.list_id=media_list.id)
            GROUP BY media_list.id ORDER BY last_files_date_add DESC,media_list.date_add DESC";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            return App::$template->parse('media_part_list', [], $result);
        } else {
            return App::$message->get('list_empty');
        }
    }
    
    public function actionFilesList(string $alias, int $page = 1): string
    {
        $view_media = get_id_by_alias('media_list', $alias, true);            
        list($media_title, $media_seo_alias, $media_descr) = App::$db->getRow("SELECT title, seo_alias, descr from media_list where id='{$view_media}'");
        $tags['list_descr'] = $media_descr;
        
        $this->title = $media_title;
        $this->breadcrumbs[] = ['title' => 'Файлы', 'url'=>'media/'];        
        $this->breadcrumbs[] = ['title' => $media_title];

        list($total) =  App::$db->getRow("SELECT count(id) from media_files where list_id='{$view_media}'");
        $pager = new Pagination($total, $page, App::$settings['media_files_per_page']);
        $tags['pager'] = $pager;
        $tags['media_list_href'] = 'media/'.$media_seo_alias.'/';

        $query = "SELECT * from media_files where list_id='" . $view_media . "' order by num asc, id asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);
        if (!$result->num_rows) {
            $content = App::$message->get('list_empty');
        } else {
            $this->tags['INCLUDE_HEAD'].='<link rel="stylesheet" href="'.App::$SUBDIR.'modules/media/player/mediaelementplayer.min.css" />';
            $this->tags['INCLUDE_JS'].='<script src="'.App::$SUBDIR.'modules/media/player/mediaelement-and-player.min.js"></script>';
            $this->tags['INCLUDE_JS'].="<script>$('audio,video').mediaelementplayer();</script>";
            $content = $this->render('media_files_list', $tags, $result);
        }
        return $content;
    }
    
    public function actionDownload(): string 
    {
        $file_id = App::$input['media_file_id'];
        
        if(is_numeric($file_id)) {
            list($file_name) = App::$db->getRow("select file_name from media_files where id='{$file_id}'");
            $file_name = App::$DIR . App::$settings['media_upload_path'] . $file_name;
            echo $file_name;
            if(file_exists($file_name)) {
                $mime_type=mime_content_type($file_name);
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $mime_type);
                header('Content-Disposition: attachment; filename=' . App::$input['download_file_name']);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_name));
                ob_clean();
                flush();
                readfile($file_name);
                $query="update media_files set download_count=download_count+1 where id='{$file_id}'";
                App::$db->query($query);
                exit;
            }
        }
        $tags['Header'] = 'Ошибка 404';
        $tags['file_name'] = App::$input['download_file_name'];
        $content = App::$message->get('file_not_found',$tags);
        header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        return $content;
    }    
    
    public function isFileExists(array $row): bool 
    {
        $file_name = App::$settings['media_upload_path'] . $row['file_name'];
        return is_file(App::$DIR . $file_name);
    }

    public function getHREF(array $row): string 
    {
        $f_info = pathinfo($row['file_name']);
        return App::$SUBDIR . "media/download?media_file_id={$row['id']}&download_file_name=" . urlencode($row['title']) . "." . $f_info["extension"];        
    }

    public function getFileSize(array $row): string 
    {
        $file_name = App::$settings['media_upload_path'] . $row['file_name'];
        if (is_file(App::$DIR . $file_name)) {
            return convert_bytes(filesize(App::$DIR . $file_name));
        } else {
            return "Файл отсутствует";
        }        
    }
    
    public function getPlayerTag(array $row): string 
    {        
        global $player_num, $player_show;
        $file_name = App::$settings['media_upload_path'] . $row['file_name'];
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
            return '
                <div class="player-tag">
                    <'.$player_tag.' id="player'.$player_num.'" src="'.App::$SUBDIR.$file_name.'" type="'.$player_type.'" controls="controls" width="320" height="'.$player_height.'"></'.$player_tag.'>		
                </div>
            ';
        }
    }   
    
}




