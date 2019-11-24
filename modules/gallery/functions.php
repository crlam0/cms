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

function show_img($row) {
    global $DIR, $settings, $SUBDIR, $input;
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

function show_list_img($row) {
    global $DIR, $settings, $SUBDIR, $input;
    // list($image_id) = my_select_row("select default_image_id from gallery_list where id='{$row["id"]}'", false);
    // $row_image = my_select_row("select * from gallery_images where id='{$image_id}'", false);
    $file_name = $DIR . $settings['gallery_upload_path'] . $row['def_file_name'];
    if ($file_name) {
        $input['icon']=true;
        $cache_file_name = gallery_get_cache_file_name($file_name, gallery_get_max_width());
        if(is_file($DIR . $cache_file_name)) {
            $URL=$cache_file_name;
        } else {
            $URL="modules/gallery/image.php?icon=1&id={$row['def_id']}";
        }
        $content='<img src="' . $SUBDIR . $URL . '" border="0" alt="'.$row['title'].'">';
    } else {
        $content = '<div class="empty_img">Изображение отсутствует: '.$row['file_name'].'</div>';
    }
    return $content;
}

function get_icons($gallery_id){
    global $DIR, $settings, $SUBDIR;
    $content='';
    $query="select * from gallery_images where gallery_id='{$gallery_id}' limit 6";
    $result = my_query($query, true);
    while($row = $result->fetch_array()){
        if (is_file($DIR . $settings['gallery_upload_path'] . $row['file_name'])) {
            $content.='<img src="' . $SUBDIR . 'modules/gallery/image.php?icon=1&id='.$row['id'].'" class="list_icon" border="0" alt="'.$row['title'].'" />';
        }
    }
    return $content;
}


