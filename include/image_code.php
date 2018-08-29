<?php 

include "common.php";

$code=$_SESSION["IMG_CODE"];

$im_w=80;
$im_h=30;
$ellipse_count=10;

Header( "Content-type:  image/png");
$im = ImageCreate ($im_w, $im_h);

$background_color = ImageColorAllocate ($im, 230, 230, 255);
// imagecolortransparent($im,$background_color);
$col1 = ImageColorAllocate ($im, 150, 150, 255);
$col2 = ImageColorAllocate($im,0,200,0);
$col3 = ImageColorAllocate ($im, 200, 200, 255);
$black= ImageColorAllocate($im,0,0,0);

imagefill ($im, 1, 1, $background_color);

for($i=0;$i<$ellipse_count;$i++){
	if(rand(0,1)){
		imageellipse($im, rand(2,$im_w-2), rand(2,$im_h-2), rand(2,$im_w-2), rand(2,$im_h-2), $col1);
		imagechar($im, 1, rand(2,$im_w-2), rand(2,$im_h-2), rand(0,9), $col2);
	}else{
		imageline($im, rand(2,$im_w-2), rand(2,$im_h-2), rand(2,$im_w-2), rand(2,$im_h-2), $col2);
		imagechar($im, 1, rand(2,$im_w-2), rand(2,$im_h-2), rand(0,9), $col1);
	}
}

for($i=0;$i<strlen($code);$i++){
	imagestring($im, rand(3,5), 5+12*$i+rand(0,4), rand(1,15), substr($code,$i,1), $black);
}


imagerectangle ($im, 1, 1, $im_w-1, $im_h-1, $col3);

ImagePng($im); 
ImageDestroy($im); 

