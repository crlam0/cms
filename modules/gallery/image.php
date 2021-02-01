<?php

include '../../include/common.php';

use classes\App;
use classes\Image;
use modules\gallery\Controller;

list($file_name, $file_type) = App::$db->getRow("select file_name,file_type from gallery_images where id='" . App::$input['id'] ."'");
$file_name = App::$DIR . App::$settings['gallery_upload_path'] . $file_name;

if (!is_file($file_name)) {
    exit();
}

if (strlen($file_type)) {
    $file_ext = Image::getFileExt($file_type);
} else {
    $file_ext = 'jpeg';
}

if (!App::$input['preview']) {
    App::$db->query("update gallery_images set view_count=view_count+1 where id='".App::$input['id']."'");
}

$crop = ($settings['gallery_fix_size']) && ($input['preview']);
if (App::$input['icon'] && App::$settings['gallery_icon_width']) {
    $crop = true;
}

if (!App::$input['clientHeight']) {
    App::$input['clientHeight'] = 800;
}
App::$input['clientHeight'] = App::$input['clientHeight'] - 210;

$max_width = Controller::getMaxWidth();
$cache_file_name = App::$DIR . Controller::$cache_path . md5($file_name.$max_width) . '.' . $file_ext;

$max_height = $max_width;
if ((!empty(App::$input['clientHeight'])) && ($max_height > App::$input['clientHeight'])) {
    $max_height = App::$input['clientHeight'];
}

if (file_exists($cache_file_name)) {
    header('Content-type: ' . $file_type);
    $img = file_get_contents($cache_file_name);
    print $img;
    exit;
}
$Image = new Image($file_name, $file_type);
if (!$Image->width) {
    die('Load error');
}
if ($crop) {
    $Image->crop($max_width, $max_width);
} else {
    $Image->resize($max_width, $max_height);
}

if (!$Image->save($cache_file_name)) {
    die('Save error');
}

header('Content-type: ' . $file_type);
$img = file_get_contents($cache_file_name);
print $img;
exit;
