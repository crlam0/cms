<?php
//exit();
include "../include/common.php";

list($file_name,$file_type) = my_select_row("select fname from cat_item_images where id='$input[id]'",true);
$file_name=$DIR.$settings[catalog_item_img_path].$file_name;

if(!is_file($file_name)){
	exit();
}

	$src = imagecreatefromjpeg($file_name);
	list($width_src, $height_src) = getimagesize($file_name);
	$max_width=$settings["catalog_item_img_preview"];

	if($max_width&&(($width_src>$max_width)||($height_src>$max_width))){
		$width=$max_width;$height=$max_width;
		if ($width_src < $height_src) {
	    	$width = ($max_width / $height_src) * $width_src;
		} else {
    		$height = ($max_width / $width_src) * $height_src;
		}
//		echo "$width_src $height_src $width $height";exit();
		$dst = imagecreatetruecolor($width, $height);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
		header('Content-type: image/jpeg');
		imagejpeg($dst, null, 75);
		exit();
	}

$img = file_get_contents($file_name);
Header( "Content-type: $file_type");
print $img;


?>