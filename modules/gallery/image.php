<?php

include '../../include/common.php';

list($file_name, $file_type) = my_select_row("select file_name,file_type from gallery_images where id='{$input['id']}'", true);
$file_name = $DIR . $settings['gallery_upload_path'] . $file_name;
$gallery_fix_size = ( $settings['gallery_fix_size']) && ($input['preview']);

if (!is_file($file_name)) {
    exit();
}

if (!$input['preview']) {
    my_query("update gallery_images set view_count=view_count+1 where id='{$input['id']}'", true);
}

if(!$input['clientHeight']){
    $input['clientHeight']=800;
}

unset($src);
if (($file_type == 'image/jpeg') || ($file_type == 'image/pjpeg')) {
    $src = imagecreatefromjpeg($file_name);
} elseif (($file_type == 'image/png')||($file_type == 'image/x-png')) {
    $src = imagecreatefrompng($file_name);
}
if ($src) {    
    list($width_src, $height_src) = getimagesize($file_name);
    if ($input['preview']) {
        $max_width = $settings['gallery_max_width_preview'];
    } else {
        $max_width = $settings['gallery_max_width'];
    }
    if ($input['icon'] && $settings['gallery_icon_width']) {
        $gallery_fix_size=true;
        $max_width = $settings['gallery_icon_width'];
    }
    if ($max_width && (($width_src > $max_width) || ($height_src > $max_width)) || ($height_src>$input['clientHeight']-210)) {
        $width = $max_width;
        $height = $max_width;
        if(!$gallery_fix_size) { 
            if ($width_src < $height_src) {
                if( (!empty($input['clientHeight'])) && ($height>$input['clientHeight']-210) ){
                    $height=$input['clientHeight']-210;
                }
                $width = ($height / $height_src) * $width_src;
            } else {
                if( (!empty($input['clientHeight'])) && ($height>$input['clientHeight']-210) ){
                    $height=$input['clientHeight']-210;
                    $width = ($height / $height_src) * $width_src;
                }
                $height = ($width / $width_src) * $height_src;
            }
        } 
	// echo "$width_src $height_src $width $height $height_full";exit();
        $dst = imagecreatetruecolor($width, $height);
    if (stristr($file_name, '.png')) {
        $alpha = imagecolorallocatealpha($src, 255, 255, 255, 127);
        if ($alpha) {
            imagecolortransparent($dst, $alpha);
            imagefill($dst, 0, 0, $alpha);
        }
    }

        if($gallery_fix_size){
            if($width_src<$height_src){
                $aspect_ratio=$width_src/$width;
                $src_h=$height*$aspect_ratio;
                $src_y=($height_src-$src_h)/2;
                $src_w = $width_src;
                $src_x = 0 ;
                
            }else{
                $aspect_ratio=$height_src/$height;
                $src_w=$width*$aspect_ratio;
                $src_x=($width_src-$src_w)/2;
                $src_h = $height_src;
                $src_y = 0 ;                
            }
        // $dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h
            imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);
        } else {
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
        }
        header('Content-type: image/jpeg');
        imagejpeg($dst, null, 75);
        exit();
    }
}
$img = file_get_contents($file_name);
Header("Content-type: {$file_type}");
print $img;
