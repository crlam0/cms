<?php
if(!isset($input)) {
    require '../../include/common.php';
}
$tags['Header']=isset($settings['catalog_header']) ? $settings['catalog_header'] : 'Магазин';
$tags['INCLUDE_HEAD'].='<link href="'.$SUBDIR.'css/catalog.css" type="text/css" rel=stylesheet />'."\n";
$tags['INCLUDE_JS'] .=  
        '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>'."\n".
        '<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.waitforimages.min.js"></script>'."\n".
        '<script type="text/javascript" src="'.$BASE_HREF.'modules/catalog/catalog.js"></script>'."\n";

if (is_array($input) && !count($input)){
    $input['part_id'] = 0;
}


if (isset($input['uri'])) {
    $params = explode('/', $input['uri']);
    $prev_id = 0;
    foreach ($params as $alias) {
        $query = "select id from cat_part where seo_alias like '$alias' and prev_id='{$prev_id}'";
        $row = my_select_row($query, true);
        if(is_numeric($row['id'])){
            $prev_id = $row['id'];
        }
    }
    $input['part_id'] = $prev_id;
}

if(isset($input['item_title'])){
    $query = "select id from cat_item where seo_alias like '{$input['item_title']}' and part_id='{$input['part_id']}'";
    $row = my_select_row($query, true);
    if(is_numeric($row['id'])){
        $input['view_item'] = $row['id'];
    }    
}

if (isset($input['part_id'])) {
    $current_part_id = $input['part_id'];
    unset($_SESSION['catalog_page']);
}

if (!isset($current_part_id)){
    $current_part_id = '0';
}    

if(isset($input['view_item'])){
    list($current_part_id)=my_select_row("select part_id from cat_item where id='{$input["view_item"]}'",1);
}

$IMG_ITEM_PATH = $DIR . $settings['catalog_item_img_path'];
$IMG_ITEM_URL = $BASE_HREF . $settings['catalog_item_img_path'];
$IMG_PART_PATH = $DIR . $settings['catalog_part_img_path'];
$IMG_PART_URL = $BASE_HREF . $settings['catalog_part_img_path'];

if (isset($input['add_buy']) && $input['cnt']) {
    if(!isset($_SESSION['BUY'][$input['item_id']]['count'])) {
        $_SESSION['BUY'][$input['item_id']]['count'] = 0;
    }
    $cnt=(int)$input['cnt'];
    if($cnt>0 && $cnt<99){
        $_SESSION['BUY'][$input['item_id']]['count']+=$cnt;
        echo 'OK';
    } else {
        echo 'ERR';
    }
    exit;
}

function show_img($tmp, $row) {
    global $IMG_ITEM_PATH, $IMG_ITEM_URL;
    if (is_file($IMG_ITEM_PATH . $row['fname'])) {
        return "<img src={$IMG_ITEM_URL}{$row['fname']} border=0>";
    } else {
        return 'Отсутствует';
    }
}

function show_price($tmp, $row) {
    global $row_part;
    $result = "<td class=price>{$row['price']}</td>";
    if ($row_part['price_cnt'] >= 2){
        $result.="<td class=price>{$row['price2']}</td>";
    }    
    if ($row_part['price_cnt'] >= 3){
        $result.="<td class=price>{$row['price3']}</td>";
    }    
    return $result;
}

function prev_part($prev_id, $deep, $arr) {
    $query = "SELECT id,title,prev_id from cat_part where id='$prev_id' order by title asc";
    $result = my_query($query);
    if(!$result->num_rows){
        return null;
    }
    $arr[$deep] = $result->fetch_array();
    if ($arr[$deep]['prev_id']){
        $arr = prev_part($arr[$deep]['prev_id'], $deep + 1, $arr);
    }    
    return $arr;
}

$tags['nav_str'].="<span class=nav_next><a href=\"" . $SUBDIR . "catalog/\" class=top>{$tags['Header']}</a></span>";
if ($current_part_id) {
    $arr = prev_part($current_part_id, 0, array());
    $arr = array_reverse($arr);
    $max_size = sizeof($arr) - 1;
    $current_part_deep=0;
    while (list ($n, $row) = @each($arr)) {
        $current_part_deep++;
        if (($n < $max_size) || (strlen($input['item_title']))) {
            $tags['nav_str'].="<span class=nav_next><a href=" . $SUBDIR . get_cat_part_href($row['id']) . ">{$row['title']}</a></span>";
            $tags['Header'].=" - {$row['title']}";
        } else {
            $tags['nav_str'].="<span class=nav_next>{$row['title']}</span>";
        }
    }
}

if ($current_part_id) {
    $row_part = my_select_row("select * from cat_part where id='{$current_part_id}'", 1);
    //	if(is_file($IMG_PART_PATH.$row[img]))echo "<img src=$IMG_PART_URL$row[img] border=0 align=left>\n";
    $tags['Header']=$row_part['title'];
}

/* 
 * ====================================================================================
 * 
 * Show popup content.
 * 
 * ====================================================================================
 */

if ($input['get_popup_content']) {
	$query="select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id) where cat_item.id='".$_GET["item_id"]."' order by b_code,title asc";
	$result=my_query($query);
	$row = $result->fetch_array();
	
	$row['price']=($row['price']?"Цена: ".$row['price']." руб":"Цена договорная.");
	
	$tags=array_merge($tags,$row);
	if(is_file($IMG_ITEM_PATH.$row['fname'])){
                $tags['default_image']="<img src={$IMG_ITEM_URL}{$row['fname']} border=0 align=left>";
	}else{
		$tags['default_image']="Изображение отсутствует.";
	}

/*	$query="select * from cat_item_images where item_id='".$_GET["item_id"]."' and id<>'$row[default_img]' order by id asc";
	$result=mysql_query($query,$conn);
	if(mysql_num_rows($result)){
		$tags[images]="<div class=images>";
		while ($row = mysql_fetch_array($result))if(is_file($IMG_ITEM_PATH.$row[fname])){
			$tags[images].="<a href={$IMG_ITEM_URL}$row[fname] target=_blank title=\"$row[descr]\"><img src={$IMG_ITEM_URL}$row[fname] border=0></a>";
		}
		$tags[images].="</div>";
	} */
	$content=get_tpl_by_title("cat_item_view",$tags,$result);
        $content.="
            <br>
            <input class=\"cnt_{$_GET["item_id"]}\" size=1 maxlength=2 value=1>
            <a class=buy_button item_id=\"{$_GET["item_id"]}\">В корзину</a>
        ";

        $json['title'] = $tags['title'];
        $json['content'] = $content;
//	echo iconv('windows-1251', 'UTF-8', $content);
        echo json_encode($json);
	exit;

}

if ($input['get_popup_image_content']) {
    
    list($default_img,$default_img_fname,$title)=my_select_row("select default_img,fname,cat_item.title from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='".$input["item_id"]."'",false);
    
    list($prev_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id<'" . $input["image_id"] . "' and id<>'{$default_img}' order by id desc limit 1", false);
    if ($input["image_id"] != $default_img){
        if ($prev_id){
            $nav_ins.= "<a image_id={$prev_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\"><< Предыдущая</a>";
        }else{     
            $nav_ins.= "<a image_id={$default_img} item_id={$input["item_id"]} file_name=\"{$default_img_fname}\" class=\"cat_image_button button\"><< Предыдущая</a>";
        }
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id>'" . $input["image_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id) {
            $nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\">Следующая >></a>";
        }
    }else{
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id) {
            $nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\">Следующая >></a>";
        }
    }

    $content.="<center><img src=\"{$SUBDIR}modules/catalog/image.php?preview=500&file_name={$input["file_name"]}&windowHeight={$input['windowHeight']}\" border=0></center>";
    if(strlen($nav_ins)){
        $content.="<br /><center>{$nav_ins}</center>";
    }
    
    $json['title'] = $title;
    $json['content'] = $content;
    echo json_encode($json);
    exit;
}


/* 
 * ====================================================================================
 * 
 * Show catalog item.
 * 
 * ====================================================================================
 */


if(strlen($input['item_title'])){
    $query="select cat_item.*,fname,cat_item_images.descr as image_descr,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.seo_alias='".$input["item_title"]."' order by b_code,title asc";
    $result=my_query($query);
    if($result->num_rows) {
        $row = $result->fetch_array();
        $item_id=$row['id'];
        $tags=array_merge($tags,$row);

        if(is_file($IMG_ITEM_PATH.$row['fname'])){
            $tags['default_image']="<img src=\"{$SUBDIR}modules/catalog/image.php?id={$row['cat_item_images_id']}&windowHeight=500&fix_size=1\" item_id={$row['id']} file_name={$row['fname']} image_id={$row['cat_item_images_id']} border=0 align=left class=cat_item_image_popup>";
        } else {
            $tags['default_image']='Изображение отсутствует';
        }

        $tags['Header']=$row['title'];
        $tags['nav_str'].="<span class=nav_next>{$row['title']}</span>";
        $part_id=$row['part_id'];

        $query="select * from cat_item_images where item_id='{$item_id}' and id<>'{$row['default_img']}' order by id asc";
        $result=my_query($query);
        $tags['images']="<div style=\"width:100%;height:1px;float:left;\">&nbsp;</div>";
        if($result->num_rows){
                $tags['images'].="<div class=item_images>";
                while ($row = $result->fetch_array())if(is_file($IMG_ITEM_PATH.$row['fname'])){
                        $tags['images'].="<div class=image_item><img class=cat_images src=\"{$SUBDIR}modules/catalog/image.php?preview=50&file_name={$row['fname']}&windowHeight=300&fix_size=1\" item_id={$item_id} file_name={$row['fname']} image_id={$row['id']} border=0></div>";
                }
                $tags['images'].="</div>";
        }

        $content.=get_tpl_by_title("cat_item_detail_view",$tags,$result);
        $content.="
        <div class=cat_back>
        <center><a href=".$SUBDIR.get_cat_part_href($part_id)." class=\"btn btn-default\"> << Назад</a></center>
        </div>";

    } else {
        $content.=my_msg_to_str('notice',[],'Товар не найден');
    }

    echo get_tpl_by_title($part['tpl_name'],$tags,"",$content);
    exit;
}



/* 
 * ====================================================================================
 * 
 * Show catalog parts.
 * 
 * ====================================================================================
 */

$content.="<div id=cat_parts>";
$subparts=0;
function sub_part($prev_id,$deep,$max_deep){
    global $tags,$content,$IMG_PART_PATH,$IMG_PART_URL,$subparts,$SUBDIR;
    if($deep)$subparts++;
    $query="SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='$prev_id' group by cat_part.id order by cat_part.num,cat_part.title asc";
    $result=my_query($query, true);
    while ($row = $result->fetch_array()){
        $pan_ins="";
        $subparts++;
        $row['href']=$SUBDIR.get_cat_part_href($row["id"]);
        $row['image']=(is_file($IMG_PART_PATH.$row['img']) ? "<img src=\"{$IMG_PART_URL}{$row['img']}\" alt=\"{$row['title']}\" title=\"{$row['title']}\">": "<br>Изображение отсутствует");
        $content.=get_tpl_by_title('cat_part_list_view',$row,$result);
        if($deep<$max_deep)sub_part($row['id'],$deep+1,$max_deep);
    }
}
sub_part($input['part_id'],0,0);
$content.="</div>";
    

/* 
 * ====================================================================================
 * 
 * Show catalog items.
 * 
 * ====================================================================================
 */

if (isset($row_part['descr'])){
    $content.="<div class=part_descr>".$row_part['descr']."</div>\n";
}


if(!isset($_SESSION["catalog_page"]))$_SESSION["catalog_page"]=1;
if(isset($input["page"])){
	$_SESSION["catalog_page"]=$input["page"];
}
list($PAGES)=my_select_row("SELECT ceiling(count(id)/{$settings['catalog_items_per_page']}) from cat_item where part_id='".$current_part_id."'",1);
$tags['pages_list']='';
if($PAGES>1){
	$tags['pages_list']="<div class=cat_pages>";
        for($i=1;$i<=$PAGES;$i++)$tags['pages_list'].=($i==$_SESSION["catalog_page"]?"[ <b>$i</b> ]&nbsp;":"[ <a href=".$SUBDIR.get_cat_part_href($current_part_id)."{$i}/>{$i}</a> ]&nbsp;");
	$tags['pages_list'].="</div>";
}
$content.=$tags['pages_list'];
$offset=$settings['catalog_items_per_page']*($_SESSION["catalog_page"]-1);	

$query="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
left join cat_item_images on (cat_item_images.id=default_img)"
.(isset($_GET["show_all"])?"":" where part_id='".$current_part_id."'")." 
group by cat_item.id   
order by cat_item.id,b_code,title asc limit $offset,{$settings['catalog_items_per_page']}";
$result=my_query($query, true);
if($result->num_rows){
    $content.="<div id=cat_items>\n";
    while ($row = $result->fetch_array()) {
        $row['item_a']='<a href="'.$SUBDIR.get_cat_part_href($row['part_id']).$row['seo_alias'].'" title="'.$row['title'].'">';
        $row['special_offer_ins']=($row['special_offer'] ? "<div class=cat_item_special_offer>Спецпредложение !</div>": "");
        $row['default_image']=(is_file($IMG_ITEM_PATH.$row['fname']) ? $row['item_a']."<img src=\"{$SUBDIR}modules/catalog/image.php?id={$row['image_id']}&windowHeight=500&fix_size=1\" alt=\"{$row['title']}\" title=\"{$row['title']}\"></a>" : "<br>Изображение отсутствует");
        $row['descr']=nl2br($row['descr']);
        $row['price']=($row['price'] ? "Цена $row[price]" : "");
        $content.=get_tpl_by_title('cat_item_list_view',$row,$result);
    }
    $content.="</div>\n";
} elseif (($current_part_id) && (!$subparts)) {
    $content.=my_msg_to_str("list_empty");
}

if($current_part_id){
    list($href_id)=my_select_row("select prev_id from cat_part where id='{$current_part_id}'", true);
    $content.="
    <div class=cat_back>
    <center><a href=".$SUBDIR.get_cat_part_href($href_id)." class=\"btn btn-default\"> << Назад</a></center>
    </div>
    ";
}


echo get_tpl_by_title($part['tpl_name'],$tags,"",$content);
