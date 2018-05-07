<?php

include "../include/common.php";

if (isset($input["id"])) {
    list($file_name, $file_type) = my_select_row("select fname from cat_item_images where id='$input[id]'", true);
    $file_name = $DIR . $settings[catalog_item_img_path] . $file_name;
} else {
    $file_name = $DIR . $settings[catalog_item_img_path] . $input["file_name"];
}
$fix_size = $input['fix_size'];

if (!is_file($file_name)) {
    exit();
}

$max_width = $settings["catalog_item_img_preview"];
if ($input["preview"])
    $max_width = $input["preview"];

unset($src);
if( (stristr($file_name,'.jpg')) || (stristr($file_name,'.jpeg')) ){
    $src = imagecreatefromjpeg($file_name);
} elseif (stristr($file_name,'.png')) {
    $src = imagecreatefrompng($file_name);
}    
list($width_src, $height_src) = getimagesize($file_name);

if ($src && $max_width && (($width_src > $max_width) || ($height_src > $max_width))) {
    $width = $max_width;
    $height = $max_width;
    if ($width_src < $height_src) {
        if ($fix_size) {
            $width = $max_width;
            $height = $max_width;
        } else {
            if ($height > $input['windowHeight'] - 210) {
                $height = $input['windowHeight'] - 210;
            }
            $width = ($height / $height_src) * $width_src;
        }
    } else {
        if ($fix_size) {
            $height = $max_width;
            $width = $max_width;
        } else {
            $height = ($width / $width_src) * $height_src;
            if ($height > $input['windowHeight'] - 210) {
                $height = $input['windowHeight'] - 210;
                $width = ($height / $height_src) * $width_src;
            }
        }
    }
//		echo "$width_src $height_src $width $height";exit();
    $dst = imagecreatetruecolor($width, $height);
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
    header('Content-type: image/jpeg');
    imagejpeg($dst, null, 75);
    exit();
}

$img = file_get_contents($file_name);
Header("Content-type: $file_type");
print $img;
