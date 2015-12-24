<?php

/*=========================================================================

Misc functions

=========================================================================*/

function have_flag($flag){
	global $_SESSION;
	if(!strlen($flag))return 1;
	return @strstr($_SESSION["FLAGS"],$flag);
}

function add_zero($summ){
	$rub=floor($summ);
	$kop=100*round($summ-$rub,2);
	if (strlen($kop)==1)$kop="0".$kop;
	return $rub.".".$kop;
}

function my_cleanstring($string,$do_remove_spaces=0){
	$string=str_replace("\r","",$string);
	$string=str_replace("\n","",$string);
	if ($do_remove_spaces==1) { $string=str_replace(" ","",$string); }
	return $string;
}

function cut_str($str, $lenght){
	$b_chars=array(" ", ",", ".", ";");
	$str=strip_tags($str);
	if(strlen($str)<=$lenght) return $str;
	$result=substr($str,0,$lenght);
	$i=$lenght;
	while (!in_array(substr($str,$i,1),$b_chars)){
		$result.=substr($str,$i,1);
		$i++;
	}
	return $result." ...";
}

function convert_bytes($in){
	$suffix="";
	if($in>1024){ $in=$in/1024;	$suffix=" Kb";
	}
	if($in>1024){ $in=$in/1024;	$suffix=" Mb";
	}
	if($in>1024){ $in=$in/1024;	$suffix=" Gb";
	}
	if(strlen($suffix)){
		$in=round($in,2);
	}
	return($in.$suffix);
}

function translit($string){
  $cyr=array(
     "Щ", "Ш", "Ч","Ц", "Ю", "Я", "Ж","А","Б","В",
     "Г","Д","Е","Ё","З","И","Й","К","Л","М","Н",
     "О","П","Р","С","Т","У","Ф","Х","Ь","Ы","Ъ",
     "Э","Є", "Ї","І",
     "щ", "ш", "ч","ц", "ю", "я", "ж","а","б","в",
     "г","д","е","ё","з","и","й","к","л","м","н",
     "о","п","р","с","т","у","ф","х","ь","ы","ъ",
     "э","є", "ї","і"
  );
  $lat=array(
     "Shch","Sh","Ch","C","Yu","Ya","J","A","B","V",
     "G","D","e","e","Z","I","y","K","L","M","N",
     "O","P","R","S","T","U","F","H","", 
     "Y","" ,"E","E","Yi","I",
     "shch","sh","ch","c","yu","ya","j","a","b","v",
     "g","d","e","e","z","i","y","k","l","m","n",
     "o","p","r","s","t","u","f","h",
     "", "y","" ,"e","e","yi","i"
  );
  for($i=0; $i<count($cyr); $i++)  {
     $c_cyr = $cyr[$i];
     $c_lat = $lat[$i];
     $string = str_replace($c_cyr, $c_lat, $string);
  }
  $string = 
  	preg_replace(
  		"/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", 
  		"\${1}e", $string);
  $string = 
  	preg_replace(
  		"/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", 
  		"\${1}'", $string);
  $string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
  $string = preg_replace("/^kh/", "h", $string);
  $string = preg_replace("/^Kh/", "H", $string);
  return $string;
}
function encodestring($string){
  $string = str_replace(array(" ","\"","&","<",">"), array(" "), $string);
  $string = preg_replace("/[_ \.,?!\[\](){}]+/", "_", $string);
  $string = preg_replace("/-{2,}/", "--", $string);
  $string = preg_replace("/_-+_/", "--", $string);
  $string = preg_replace("/[_\-]+$/", "", $string);
  $string = translit($string);
  $string = strtolower($string);
  $string = preg_replace("/j{2,}/", "j", $string);
  $string = preg_replace("/[^0-9a-z_\-]+/", "", $string);
  return $string;
}

$validImageTypes = array("image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/x-png");

function move_uploaded_image($src_file, $dst_file, $max_width = 0) {
    global $settings;
    if (!is_file($src_file["tmp_name"])) {
        print_err("Файл отсутствует !");
        return false;
    }
    $validImageTypes = array('image/pjpeg', 'image/jpeg', 'image/gif', "image/png", "image/x-png");
    if(!in_array($src_file['type'],$validImageTypes)){
        print_err("Неверный тип файла !");
        return false;
    }
//	print_arr($src_file);
    unset($src);
    if (($src_file["type"] == 'image/jpeg') || ($src_file["type"] == 'image/pjpeg')) {
        $src = imagecreatefromjpeg($src_file["tmp_name"]);
    } elseif ($src_file["type"] == 'image/x-png') {
        $src = imagecreatefrompng($src_file['tmp_name']);
    }
    if ($src) {
        list($width_src, $height_src) = getimagesize($src_file["tmp_name"]);
        if ($max_width && (($width_src > $max_width) || ($height_src > $max_width))) {
            $width = $max_width;
            $height = $max_width;
            if ($width_src < $height_src) {
                $width = ($max_width / $height_src) * $width_src;
            } else {
                $height = ($max_width / $width_src) * $height_src;
            }
            $dst = imagecreatetruecolor($width, $height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
            @imagejpeg($dst, $dst_file, 100);
            return is_file($dst_file);
        }
    }
    return move_uploaded_file($src_file["tmp_name"], $dst_file);
}

function show_month($month,$service_id) {
    global $_SESSION, $server;
    $month_names = array(1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель', 5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь');
    $day_names = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
    $allow_past = false;
    $date_format = 'd.m.Y';

    $pd = mktime(0, 0, 0, $month, 1, date('Y')); // timestamp of the first day
    $zd = -(date('w', $pd) ? (date('w', $pd) - 1) : 6) + 1; // monday before
    $kd = date('t', $pd); // last day of moth
    echo '
        <div class="month_title">
          <div class="month_name">' . $month_names[date('n', mktime(0, 0, 0, $month, 1, date('Y')))] . ' ' . date('Y', mktime(0, 0, 0, $month, 1, date('Y'))) . '</div>
          <div class="r"></div>
        </div>';
    for ($d = 0; $d < 7; $d++) {
        echo '
        <div class="week_day">' . $day_names[$d] . '</div>';
    }
    echo '
        <div class="r"></div>';
    for ($d = $zd; $d <= $kd; $d++) {
        $i = mktime(0, 0, 0, $month, $d, date('Y'));
        if ($i >= $pd) {
            $today = (date('Ymd') == date('Ymd', $i)) ? '_today' : '';
            $minulost = (date('Ymd') >= date('Ymd', $i + 86400)) && !$allow_past;
            echo '<div class="day' . $today . '">';
            $day = date('j', $i);
//             if ((date('Ymd', $i)>date('Ymd')) ) {
//                echo "<a href=\"" . $server["PHP_SELF"] . "?sel_service=" . $service_id . "&sel_day=" . date('Y-m-d', $i) . "\">$day</a>";
//            } else {
                echo date('j', $i);
//            }
            echo '</div>';
        } else {
            echo '
        <div class="no_day">&nbsp;</div>';
        }
        if (date('w', $i) == 0 && $i >= $pd) {
            echo '<div class="r"></div>';
        }
    }
}

/*=========================================================================

Menu functions

=========================================================================*/


function get_article_list_href($list_id,$row = array()){
    global $SUBDIR;
    if($row["id"])$list_id=$row["id"];
    $query="select seo_alias from article_list where id='{$list_id}'";
    $result=my_query($query,$conn,1);
    list($seo_alias)=$result->fetch_array();
    if(strlen($seo_alias)) {
        return "article/".$seo_alias."/";
    }else{
        return "article/?view_items=".$list_id;        
    }
}

function get_article_href($article_id,$row = array()){
    global $SUBDIR;
    if($row["id"])$article_id=$row["id"];
    $query="select seo_alias,list_id from article_item where id='{$article_id}'";
    $result=my_query($query,$conn,1);
    list($seo_alias,$list_id)=$result->fetch_array();
    if(strlen($seo_alias)) {
        return get_article_list_href($list_id).$seo_alias."/";
    }else{
        return "article/?view=".$article_id;        
    }
}

function get_media_list_href($list_id,$row = array()){
    global $SUBDIR;
    if($row["id"])$list_id=$row["id"];
    $query="select seo_alias from media_list where id='{$list_id}'";
    $result=my_query($query,$conn,1);
    list($seo_alias)=$result->fetch_array();
    if(strlen($seo_alias)) {
        return "media/".$seo_alias."/";
    }else{
        return "media/index.php?view_files=1&id=".$list_id;        
    }
}

function cat_prev_part($prev_id,$deep,$arr){
    $query="SELECT id,title,prev_id,seo_alias from cat_part where id='{$prev_id}' order by title asc";
    $result=my_query($query);
    $arr[$deep]=$result->fetch_array();
    if($arr[$deep]['prev_id'])$arr=cat_prev_part($arr[$deep]['prev_id'],$deep+1,$arr);
    return $arr;
}

function get_cat_part_href($list_id,$row = array()){
    global $SUBDIR;
    if(is_numeric($row['id'])){
        $part_id=$row['id'];
    }else{
        $part_id=$list_id;
    }
    unset($arr);
    $uri='catalog/';
    if($part_id){
        $arr=cat_prev_part($part_id,0,$arr);
        $arr=array_reverse($arr);
        while (list ($n, $row) = @each ($arr)){
            $uri.=$row['seo_alias'].'/';
        }
    }
    return $uri;
}

function get_gallery_list_href($list_id,$row = array()){
    global $SUBDIR;
    if($row["id"])$list_id=$row["id"];
    $query="select seo_alias from gallery_list where id='{$list_id}'";
    $result=my_query($query,$conn,1);
    list($seo_alias)=$result->fetch_array();
    if(strlen($seo_alias)) {
        return "gallery/".$seo_alias."/";
    }else{
        return "gallery/index.php?view_gallery=1&id=".$list_id;        
    }
}
function get_post_href($post_id,$row){
    global $SUBDIR;
    if($row['id'])$post_id=$row['id'];
    if(strlen($row['seo_alias'])) {
        return "blog/".$row['seo_alias']."/";
    }else{
        return "blog/"."?view_post=".$row['id'];
    }
}

function get_menu_href($tmp,$row){
    switch ($row["target_type"]){
        case "":
            return $row["href"];
            break;
        case "link":
            return $row["href"];
            break;
        case "article_list":
            return get_article_list_href($row["target_id"]);
            break;
        case "article":
            return get_article_href($row["target_id"]);
            break;
        case "media_list":
            return get_media_list_href($row["target_id"]);
            break;
        case "cat_part":
            return get_cat_part_href($row["target_id"]);
            break;
        case "gallery_list":
            return get_gallery_list_href($row["target_id"]);
            break;
    }
}

function replace_base_href($content,$direction = false){
    global $server,$SUBDIR;
    if($direction){
        return str_replace("http://".$server["HTTP_HOST"].$SUBDIR, "[%SUBDIR%]", $content);
    }else{
        return str_replace("[%SUBDIR%]","http://".$server["HTTP_HOST"].$SUBDIR, $content);        
    }
}

?>
