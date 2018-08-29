<?php 
include "common.php";

$query="select count(id),count(distinct remote_addr) from visitor_log";
list($hits,$unique)=my_select_row($query,true);
while(strlen($hits)<=6)$hits="0".$hits;

$im_w=80;
$im_h=25;

$im = ImageCreate ($im_w, $im_h);

$background_color = ImageColorAllocate ($im, 255, 255, 255);
$red = ImageColorAllocate ($im, 255, 50, 50);
$col1 = ImageColorAllocate ($im, 100, 100, 100);
$black= ImageColorAllocate($im,0,0,0);

imagefill ($im, 0, 0, $background_color);

imagestring($im,3,10,1,$hits,$black);
imagestring($im,1,10,14,"Unique: ".$unique,$red);

imagerectangle ($im, 0, 0, $im_w-1, $im_h-1, $col1);

Header( "Content-type:  image/gif");
ImageGif($im); 
ImageDestroy($im); 

