<?php

function gallery_get_max_width() {
    global $input, $settings;
    
    if ($input['preview']) {
        $max_width = $settings['gallery_max_width_preview'];
    } else {
        $max_width = $settings['gallery_max_width'];
    }
    if ($input['icon'] && $settings['gallery_icon_width']) {
        $max_width = $settings['gallery_icon_width'];
    }
    if ($input['width'] && is_integer($input['width'])) {
        $max_width = $input['width'];
    }
    
    return $max_width;
}

function gallery_get_cache_file_name($file_name, $max_width) {
    return 'var/cache/gallery/' . md5($file_name.$max_width) . '.jpeg';
}

function show_img($tmp, $row) {
    global $DIR, $settings, $SUBDIR, $server, $input;
    $file_name = $DIR . $settings['gallery_upload_path'] . $row['file_name'];
    if (is_file($file_name)) {        
        $content='';
        $input['preview']=true;
        $cache_file_name = gallery_get_cache_file_name($file_name, gallery_get_max_width());
        if(is_file($DIR . $cache_file_name)) {
            $URL=$cache_file_name;
        } else {
            $URL="modules/gallery/image.php?preview=1&id={$row['id']}";
        }
        $content.='<img src="' . $SUBDIR . $URL . '" border="0" item_id="'.$row['id'].'" class="gallery_popup" alt="'.$row['title'].'">';
    } else {
        $content = '<div class="empty_img">Изображение отсутствует: '.$row['file_name'].'</div>';
    }
    return $content;
}

function show_list_img($tmp, $row) {
    global $DIR, $settings, $SUBDIR, $input;
    list($image_id) = my_select_row("select default_image_id from gallery_list where id='{$row["id"]}'", false);
    $row_image = my_select_row("select * from gallery_images where id='{$image_id}'", false);
    $file_name = $DIR . $settings['gallery_upload_path'] . $row_image['file_name'];
    if ($file_name) {
        $input['icon']=true;
        $cache_file_name = gallery_get_cache_file_name($file_name, gallery_get_max_width());
        if(is_file($DIR . $cache_file_name)) {
            $URL=$cache_file_name;
        } else {
            $URL="modules/gallery/image.php?icon=1&id={$row_image["id"]}";
        }
        $content='<img src="' . $SUBDIR . $URL . '" border="0" alt="'.$row['title'].'">';
    } else {
        $content = '<div class="empty_img">Изображение отсутствует: '.$row['file_name'].'</div>';
    }
    return $content;
}



