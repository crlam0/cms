<?php

include '../../include/common.php';
include 'functions.php';

use Classes\Image;

list($file_name, $file_type) = my_select_row("select file_name,file_type from gallery_images where id='{$input['id']}'", true);
$file_name = $DIR . $settings['gallery_upload_path'] . $file_name;

if (!is_file($file_name)) {
    exit();
}

if (!$input['preview']) {
    my_query("update gallery_images set view_count=view_count+1 where id='{$input['id']}'", true);
}

$crop = ($settings['gallery_fix_size']) && ($input['preview']);
if ($input['icon'] && $settings['gallery_icon_width']) {
    $crop = true;
}

if (!$input['clientHeight']) {
    $input['clientHeight'] = 800;
}
$input['clientHeight'] = $input['clientHeight'] - 210;

$max_width = gallery_get_max_width();
$cache_file_name = $DIR . gallery_get_cache_file_name($file_name, $max_width);

$max_height = $max_width;
if ((!empty($input['clientHeight'])) && ($max_height > $input['clientHeight'])) {
    $max_height = $input['clientHeight'];
}

if(file_exists($cache_file_name)) {
    header('Content-type: ' . $file_type);
    $img = file_get_contents($cache_file_name);
    print $img;
    exit;
}
$Image = new Image($file_name,$file_type);
if(!$Image->width) {
    print_error('Load error');
    exit;
}
if($crop) {
    $result = $Image->crop($max_width,$max_width);
} else {
    $result = $Image->resize($max_width,$max_height);
}    
if($result) {
    if(!$Image->save($cache_file_name)) {
        print_error('Save error');
        exit;    
    }
} else { 
    $cache_file_name = $file_name;
}

header('Content-type: ' . $file_type);
$img = file_get_contents($cache_file_name);
print $img;
exit;
