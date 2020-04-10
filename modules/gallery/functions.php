<?php

use Classes\App;
use Classes\Image;

function gallery_get_max_width() {    
    if (App::$input['preview']) {
        $max_width = App::$settings['gallery_max_width_preview'];
    } else {
        $max_width = App::$settings['gallery_max_width'];
    }
    if (App::$input['icon'] && App::$settings['gallery_icon_width']) {
        $max_width = App::$settings['gallery_icon_width'];
    }
    if (App::$input['width'] && is_integer(App::$input['width'])) {
        $max_width = App::$input['width'];
    }    
    return $max_width;
}

function gallery_get_cache_file_name($file_name, $max_width) {
    return 'var/cache/gallery/' . md5($file_name.$max_width) . '.jpeg';
}

function show_img($row) {
    App::$input['preview']=true;
    $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $row['file_name'];
    $image = new Image($file_name, $row['file_type']);
    return $image->getHTML($row, 'var/cache/gallery/', 'gallery_popup', 'modules/gallery/image.php?preview=1&id=',gallery_get_max_width());
}

function show_list_img($row) {
    if(!$row['def_file_name']) {
        return '';
    }
    App::$input['icon']=true;    
    $row['file_name'] = $row['def_file_name'];
    $row['id'] = $row['def_id'];
    $file_name = App::$DIR . App::$settings['gallery_upload_path'] . $row['file_name'];
    $image = new Image($file_name, $row['def_file_type']);
    return $image->getHTML($row, 'var/cache/gallery/','','modules/gallery/image.php?icon=1&id=', gallery_get_max_width());
}

function get_icons($gallery_id){
    global $DIR, $SUBDIR;
    $content='';
    $query="select * from gallery_images where gallery_id='{$gallery_id}' limit 6";
    $result = App::$db->query($query);
    while($row = $result->fetch_array()){
        if (is_file($DIR . App::$settings['gallery_upload_path'] . $row['file_name'])) {
            $content.='<img src="' . $SUBDIR . 'modules/gallery/image.php?icon=1&id='.$row['id'].'" class="list_icon" border="0" alt="'.$row['title'].'" />';
        }
    }
    return $content;
}


