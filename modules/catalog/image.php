<?php

include "../../include/common.php";
include 'functions.php';

if (isset($input["id"])) {
    list($file_name,$file_type) = my_select_row("select fname,file_type from cat_item_images where id='{$input['id']}'", true);
    $file_name = $DIR . $settings['catalog_item_img_path'] . $file_name;
} else {
    $file_name = $DIR . $settings['catalog_item_img_path'] . $input["file_name"];
}
$fix_size = $input['fix_size'];

if (!is_file($file_name)) {
    exit();
}

if (!$input['clientHeight']) {
    $input['clientHeight'] = 800;
}
$input['clientHeight'] = $input['clientHeight'] - 210;

$max_width = $settings["catalog_item_img_preview"];
if ($input["preview"]) {
    $max_width = $input["preview"];
}

$cache_file_name = $DIR . get_cache_file_name($file_name, $max_width);



unset($src);
if ((stristr($file_name, '.jpg')) || (stristr($file_name, '.jpeg'))) {
    $src = imagecreatefromjpeg($file_name);
    $file_type = isset($file_type) ? $file_type : 'image/jpeg';
} elseif (stristr($file_name, '.png')) {
    $src = imagecreatefrompng($file_name);
    $file_type = isset($file_type) ? $file_type : 'image/png';
}
if ($src) {
    list($width_src, $height_src) = getimagesize($file_name);
    if ($src && $max_width && (($width_src > $max_width) || ($height_src > $max_width))) {
        if (file_exists($cache_file_name)) {
            header('Content-type: image/jpeg');
            $img = file_get_contents($cache_file_name);
            print $img;
            exit;
        }
        $width = $max_width;
        $height = $max_width;
        if (!$fix_size) {
            if ($width_src < $height_src) {
                if ((!empty($input['clientHeight'])) && ($height > $input['clientHeight'])) {
                    $height = $input['clientHeight'];
                }
                $width = ($height / $height_src) * $width_src;
            } else {
                if ((!empty($input['clientHeight'])) && ($height > $input['clientHeight'])) {
                    $height = $input['clientHeight'];
                    $width = ($height / $height_src) * $width_src;
                }
                $height = ($width / $width_src) * $height_src;
            }
        }
//		echo "$width_src $height_src $width $height";exit();
        $dst = imagecreatetruecolor($width, $height);
        if (stristr($file_name, '.png')) {
            $alpha = imagecolorallocatealpha($src, 255, 255, 255, 127);
            if ($alpha) {
                imagecolortransparent($dst, $alpha);
                imagefill($dst, 0, 0, $alpha);
            }
        }
        if ($fix_size) {
            if ($width_src < $height_src) {
                $aspect_ratio = $width_src / $width;
                $src_h = $height * $aspect_ratio;
                $src_y = ($height_src - $src_h) / 2;
                $src_w = $width_src;
                $src_x = 0;
            } else {
                $aspect_ratio = $height_src / $height;
                $src_w = $width * $aspect_ratio;
                $src_x = ($width_src - $src_w) / 2;
                $src_h = $height_src;
                $src_y = 0;
            }
            imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);
        } else {
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
        }
        if(!file_exists(dirname($cache_file_name))) {
            if (!mkdir(dirname($cache_file_name), 0755, true)) {
                die('Не удалось создать директории...');
            }
        }
        imagejpeg($dst, $cache_file_name, 75);
        header('Content-type: image/jpeg');
        $img = file_get_contents($cache_file_name);
        print $img;
        exit;
    }
}
$img = file_get_contents($file_name);
Header("Content-type: {$file_type}");
print $img;
