<?php
$tags[Header]="";
include "../include/common.php";

if (!count($input))
    $input[part_id] = 0;

if (isset($input["uri"])) {
    $params = explode("/", $input["uri"]);
    $prev_id = 0;
    foreach ($params as $alias) {
        $query = "select id from cat_part where seo_alias like '$alias' and prev_id='{$prev_id}'";
        $row = my_select_row($query, true);
        $prev_id = $row["id"];
    }
    $input[part_id] = $prev_id;
}

if (isset($input[part_id])) {
    $_SESSION["PART_ID"] = $input[part_id];
    unset($_SESSION["catalog_page"]);
}

if (!isset($_SESSION["PART_ID"]))
    $_SESSION["PART_ID"] = "0";

$IMG_ITEM_PATH = $DIR . $settings[catalog_item_img_path];
$IMG_ITEM_URL = $BASE_HREF . $settings[catalog_item_img_path];
$IMG_PART_PATH = $DIR . $settings[catalog_part_img_path];
$IMG_PART_URL = $BASE_HREF . $settings[catalog_part_img_path];

if (isset($input["add_buy"])) {
    $_SESSION["BUY"][$input["item_id"]]["count"]+=$input["cnt"];
    echo "OK";
    exit;
}

function show_img($tmp, $row) {
    global $IMG_ITEM_PATH, $IMG_ITEM_URL;
    if (is_file($IMG_ITEM_PATH . $row[fname])) {
        return "<img src={$IMG_ITEM_URL}$row[fname] border=0>";
    } else {
        return "Отсутствует";
    }
}

function show_price($tmp, $row) {
    global $row_part;
    $result = "<td class=price>$row[price]</td>";
    if ($row_part[price_cnt] >= 2)
        $result.="<td class=price>$row[price2]</td>";
    if ($row_part[price_cnt] >= 3)
        $result.="<td class=price>$row[price3]</td>";
    return $result;
}

if(isset($_GET["view_item"])){
	list($_SESSION["PART_ID"])=my_select_row("select part_id from cat_item where id='{$_GET["view_item"]}'",1);
}

if(isset($_GET["show_all"]))$_SESSION["PART_ID"]=0;


function prev_part($prev_id, $deep) {
    global $conn, $arr;
    $query = "SELECT id,title,prev_id from cat_part where id='$prev_id' order by title asc";
    $result = my_query($query, $conn);
    $arr[$deep] = $result->fetch_array();
    if ($arr[$deep]["prev_id"])
        prev_part($arr[$deep]["prev_id"], $deep + 1);
}

// $tags[nav_str].="<span class=nav_next><a href=\"" . $SUBDIR . "catalog/\" class=top>$tags[Header]</a></span>";
if ($_SESSION["PART_ID"]) {
    prev_part($_SESSION["PART_ID"], 0);
    $arr = array_reverse($arr);
    $max_size = sizeof($arr) - 1;
    while (list ($n, $row) = @each($arr)) {
        if (($n < $max_size) || (strlen($input["item_title"]))) {
            $tags[nav_str].="<span class=nav_next><a href=" . $SUBDIR . get_cat_part_href($row[id]) . ">$row[title]</a></span>";
            $tags[Header].="$row[title]";
        } else {
            $tags[nav_str].="<span class=nav_next>$row[title]</span>";
        }
    }
}

/*
=======================================================================================================================================

Генерация HTML для POPUP

=======================================================================================================================================
*/

if ($input['get_popup_content']) {
    
    list($default_img,$default_img_fname)=my_select_row("select default_img,fname from cat_item left join cat_item_image on (cat_item_image.id=default_img) where cat_item.id='".$input["item_id"]."'",false);
    
    list($prev_id,$fname) = my_select_row("select id,fname from cat_item_image where item_id='" . $input["item_id"] . "' and id<'" . $input["image_id"] . "' and id<>'{$default_img}' order by id desc limit 1", false);
    if ($input["image_id"] != $default_img){
        if ($prev_id){
            $nav_ins.= "<a image_id={$prev_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\"><< Предыдущая</a>";
        }else{     
            $nav_ins.= "<a image_id={$default_img} item_id={$input["item_id"]} file_name=\"{$default_img_fname}\" class=\"cat_image_button button\"><< Предыдущая</a>";
        }
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_image where item_id='" . $input["item_id"] . "' and id>'" . $input["image_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id)$nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\">Следующая >></a>";
    }else{
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_image where item_id='" . $input["item_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id)$nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button button\">Следующая >></a>";
    }

    if(strlen($nav_ins))$content.="<center>{$nav_ins}</center><br />";

    $content.="<center><img src=\"{$SUBDIR}catalog/image.php?preview=500&file_name={$input["file_name"]}&windowHeight={$input['windowHeight']}\" border=0></center>";
    
    echo $content;    
    exit;
}

if ($_SESSION['PART_ID']) {
    $row_part = my_select_row("select title from cat_part where id='{$_SESSION["PART_ID"]}'", 1);
    //	if(is_file($IMG_PART_PATH.$row[img]))echo "<img src=$IMG_PART_URL$row[img] border=0 align=left>\n";
    $tags[Header].=$row_part['title'];
}

/*
=======================================================================================================================================

Вывод данных о товаре

=======================================================================================================================================
*/

if(strlen($input['item_title'])){
    $query="select cat_item.*,fname,cat_item_image.descr as image_descr,cat_item_image.id as cat_item_image_id from cat_item left join cat_item_image on (cat_item_image.id=default_img) where cat_item.seo_alias='".$input["item_title"]."' order by b_code,title asc";
    $result=my_query($query);
    $row = $result->fetch_array();
    $item_id=$row['id'];
    $tags=array_merge($tags,$row);
    if(is_file($IMG_ITEM_PATH.$row[fname]))$tags[default_image]="<img src=\"{$SUBDIR}catalog/image.php?id={$row['cat_item_image_id']}&windowHeight=500\" item_id={$row['id']} file_name={$row[fname]} image_id={$row[cat_item_image_id]} border=0 align=left class=cat_item_image_popup>";

    $tags['Header']=$row['title'];
    $tags['nav_str'].="<span class=nav_next>{$row['title']}</span>";

    $query="select * from cat_item_image where item_id='{$item_id}' and id<>'{$row['default_img']}' order by id asc";
    $result=my_query($query);
    $tags[images].="<div style=\"width:100%;height:1px;float:left;\">&nbsp;</div>";
    if($result->num_rows){
            $tags[images].="<div class=item_images>";
            while ($row = $result->fetch_array())if(is_file($IMG_ITEM_PATH.$row[fname])){
                    $tags[images].="<div class=image_item><img class=cat_images src=\"{$SUBDIR}catalog/image.php?preview=80&file_name=$row[fname]&windowHeight=300&fix_size=1\" item_id={$item_id} file_name={$row[fname]} image_id={$row[id]} border=0></div>";
            }
            $tags[images].="</div>";
    }
    
    ob_start();
    include_once($DIR . "catalog/yandex_map.php");
    $tags['yandex_map'] = ob_get_contents();
    ob_end_clean();

    $content.=get_tpl_by_title("cat_item_detail_view",$tags,$result);
    $content.="
    <div class=cat_back>
    <center><a href=".$SUBDIR.get_cat_part_href($_SESSION["PART_ID"])." class=button> << Назад</a></center>
    </div>";
    
    $tags['head_inc'] =  
        '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>
        <script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.waitforimages.min.js"></script>
        <script type="text/javascript" src="'.$BASE_HREF.'catalog/catalog.js"></script>
        <script src="//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU" type="text/javascript"></script>    
        ';

    echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
    exit;
}

/*
=======================================================================================================================================

Рекурсивный вовод разделов каталога

=======================================================================================================================================
*/

$content.="<div id=cat_parts>";
$subparts=0;
function sub_part($prev_id,$deep,$max_deep){
    global $conn,$tags,$content,$IMG_PART_PATH,$IMG_PART_URL,$subparts,$SUBDIR;
    if($deep)$subparts++;
    $query="SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='$prev_id' group by cat_part.id order by cat_part.num,cat_part.title asc";
    $result=my_query($query,$conn,1);
    while ($row = $result->fetch_array()){
        $pan_ins="";
        $subparts++;
        $row['href']=$SUBDIR.get_cat_part_href($row["id"]);
        $row['image']=(is_file($IMG_PART_PATH.$row[img]) ? "<img src=\"{$IMG_PART_URL}{$row['img']}\" alt=\"{$row['title']}\" title=\"{$row['title']}\">": "<br>Изображение отсутствует");
        $content.=get_tpl_by_title('cat_part_list_view',$row,$result);
        if($deep<$max_deep)sub_part($row[id],$deep+1,$max_deep);
    }
}
sub_part($_SESSION["PART_ID"],0,0);
$content.="</div>";

/*
=======================================================================================================================================

Постраничный вывод товаров

=======================================================================================================================================
*/

if(!isset($_SESSION["catalog_page"]))$_SESSION["catalog_page"]=1;
if(isset($input["page"])){
	$_SESSION["catalog_page"]=$input["page"];
}
list($PAGES)=my_select_row("SELECT ceiling(count(id)/$settings[catalog_items_per_page]) from cat_item where part_id='".$_SESSION["PART_ID"]."'",1);
if($PAGES>1){
	$tags[pages_list]="<div class=cat_pages>";
	for($i=1;$i<=$PAGES;$i++)$tags[pages_list].=($i==$_SESSION["catalog_page"]?"[ <b>$i</b> ]&nbsp;":"[ <a href=".$_SERVER["PHP_SELF"]."?page=$i>$i</a> ]&nbsp;");
	$tags[pages_list].="</div>";
}
$content.=$tags[pages_list];
$offset=$settings[catalog_items_per_page]*($_SESSION["catalog_page"]-1);	

$query="select cat_item.*,fname,cat_item.id as item_id,cat_item_image.id as image_id,cat_item.seo_alias from cat_item 
left join cat_item_image on (cat_item_image.id=default_img)"
.(isset($_GET["show_all"])?"":" where part_id='".$input[part_id]."'")." 
group by cat_item.id
order by cat_item.num,b_code,title asc limit $offset,$settings[catalog_items_per_page]";
$result=my_query($query,$conn,1);
if($result->num_rows){
    $content.='<div id=cat_items>';
    while ($row = $result->fetch_array()){
        $row['item_a']='<a href="'.$SUBDIR.get_cat_part_href($_SESSION["PART_ID"]).$row['seo_alias'].'" title="'.$row['title'].'">';
        $row['special_offer_ins']=($row['special_offer'] ? "<div class=cat_item_special_offer>Специальное предложение !</div>": "");
        $row['default_image']=(is_file($IMG_ITEM_PATH.$row['fname']) ? $row['item_a']."<img src=\"{$SUBDIR}catalog/image.php?id={$row[image_id]}&windowHeight=500&fix_size=0\" alt=\"{$row['title']}\" title=\"{$row['title']}\"></a>" : "<br>Изображение отсутствует");
        $row['descr']=nl2br($row['descr']);
        $row['price']=($row[price] ? "Цена $row[price]" : "");
        $content.=get_tpl_by_title('cat_item_list_view',$row,$result);
    }
    $content.='</div>';
}elseif( ($_SESSION['PART_ID']) && (!$subparts)){
    $content.=my_msg_to_str('list_empty');
}

if($_SESSION["PART_ID"]){
    if (strlen($row_part[descr]))
        $content.="<div class=part_descr>".nl2br($row_part['descr'])."</div>\n";
    if($subparts){
	$href_id=0;
    }else{
	list($href_id)=my_select_row("select prev_id from cat_part where id='".$_SESSION["PART_ID"]."'", 1);
    }
//    $content.="<div class=cat_back><center><a href=".$SUBDIR.get_cat_part_href($href_id)." class=button> << Назад</a></center></div>";
}


/*
if($_SESSION["PART_ID"]==0){
    $query = "select title,content from article_item where id='35'";
    $result = my_query($query, $conn);
    list($title, $text) = $result->fetch_array();
    $content=$text."".$content;
}
*/

$tags['head_inc'] =  
    '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>
    <script type="text/javascript" src="'.$BASE_HREF.'catalog/catalog.js"></script>
    ';

echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
?>
