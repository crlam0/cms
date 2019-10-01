<?php

include "../../include/common.php";
include 'functions.php';

use Classes\Image;

if (isset($input['id'])) {
    list($file_name,$file_type) = my_select_row("select fname,file_type from cat_item_images where id='{$input['id']}'", true);
    $file_name = $DIR . $settings['catalog_item_img_path'] . $file_name;
} else {
    $file_name = $DIR . $settings['catalog_item_img_path'] . $input['file_name'];
}
$crop = $input['fix_size'];

if (!is_file($file_name)) {
    exit();
}

if (!$input['clientHeight']) {
    $input['clientHeight'] = 800;
}
$input['clientHeight'] = $input['clientHeight'] - 210;

$max_width = $settings['catalog_item_img_preview'];
if ($input['preview']) {
    $max_width = $input['preview'];
}

$cache_file_name = $DIR . get_cache_file_name($file_name, $max_width);

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


