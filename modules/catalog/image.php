<?php

include "../../include/common.php";

use classes\App;
use classes\Image;
use modules\catalog\controllers\Controller;

if (isset(App::$input['id'])) {
    list($file_name, $file_type) = App::$db->getRow("select file_name,file_type from cat_item_images where id='" . App::$input['id'] ."'");
} else {
    list($file_name, $file_type) = App::$db->getRow("select file_name,file_type from cat_item_images where file_name='" . App::$input['file_name'] ."'");
}

$crop = App::$input['crop'];

if (!App::$input['clientHeight']) {
    App::$input['clientHeight'] = 800;
}
$input['clientHeight'] = App::$input['clientHeight'] - 210;

$max_width = App::$settings['catalog_item_img_max_width'];

if (App::$input['preview']) {
    $max_width = App::$input['preview'];
}

$cache_file_name = App::$DIR . Controller::getCacheFilename($file_name, $file_type, $max_width);

// echo $cache_file_name;exit;

if (file_exists($cache_file_name)) {
    header('Content-type: ' . $file_type);
    $img = file_get_contents($cache_file_name);
    print $img;
    exit;
}
$file_name = App::$DIR . App::$settings['catalog_item_img_path'] . $file_name;

$Image = new Image($file_name, $file_type);
if (!$Image->width) {
    die('Load error');
}

if ($crop) {
    $result = $Image->crop($max_width, $max_width);
} else {
    $result = $Image->resize($max_width, $max_width);
}

if (!$Image->save($cache_file_name)) {
    die('Save error');
}

header('Content-type: ' . $file_type);
$img = file_get_contents($cache_file_name);
print $img;
exit;
