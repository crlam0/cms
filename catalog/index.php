<?php
$tags['Header']="Каталог техники";
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

function prev_part($prev_id, $deep) {
    global $conn, $arr;
    $query = "SELECT id,title,prev_id from cat_part where id='$prev_id' order by title asc";
    $result = my_query($query, $conn);
    $arr[$deep] = $result->fetch_array();
    if ($arr[$deep]["prev_id"])
        prev_part($arr[$deep]["prev_id"], $deep + 1);
}

if(isset($_GET["view_item"])){
	list($_SESSION["PART_ID"])=my_select_row("select part_id from cat_item where id='{$_GET["view_item"]}'",1);
}

if(isset($_GET["show_all"]))$_SESSION["PART_ID"]=0;

$tags[nav_str].="<span class=nav_next><a href=\"" . $SUBDIR . "catalog/\" class=top>$tags[Header]</a></span>";
if ($_SESSION["PART_ID"]) {
    prev_part($_SESSION["PART_ID"], 0);
    $arr = array_reverse($arr);
    $max_size = sizeof($arr) - 1;
    while (list ($n, $row) = @each($arr)) {
        if (($n < $max_size) || (strlen($input["item_title"]))) {
            $tags[nav_str].="<span class=nav_next><a href=" . $SUBDIR . get_cat_part_href($row[id]) . ">$row[title]</a></span>";
            $tags[Header].=" - $row[title]";
        } else {
            $tags[nav_str].="<span class=nav_next>$row[title]</span>";
        }
    }
}

if ($input['get_popup_content']) {
    $query = "select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id) where cat_item.id='" . $input["item_id"] . "' order by b_code,title asc";
    $result = my_query($query, $conn);
    $row = $result->fetch_array();

    $row[price] = ($row[price] ? "Цена: " . $row[price] . " руб" : "Цена договорная.");

    $tags = array_merge($tags, $row);
    if (is_file($IMG_ITEM_PATH . $row[fname])) {
        $tags[default_image] = "<img src={$IMG_ITEM_URL}$row[fname] border=0>";
    } else {
        $tags[default_image] = "Изображение отсутствует.";
    }

    /* 	$query="select * from cat_item_images where item_id='".$_GET["item_id"]."' and id<>'$row[default_img]' order by id asc";
      $result=my_query($query,$conn);
      if($result->num_rows){
      $tags[images]="<div class=images>";
      while ($row = $result->fetch_array())if(is_file($IMG_ITEM_PATH.$row[fname])){
      $tags[images].="<a href={$IMG_ITEM_URL}$row[fname] target=_blank title=\"$row[descr]\"><img src={$IMG_ITEM_URL}$row[fname] border=0></a>";
      }
      $tags[images].="</div>";
      } */
    $content = get_tpl_by_title("cat_item_image_view", $tags, $result);
//	echo iconv('windows-1251', 'UTF-8', $content);
    echo $content;
    exit;
}

if ($_SESSION["PART_ID"]) {
    $row_part = my_select_row("select * from cat_part where id='{$_SESSION["PART_ID"]}'", 1);
    //	if(is_file($IMG_PART_PATH.$row[img]))echo "<img src=$IMG_PART_URL$row[img] border=0 align=left>\n";
    $tags[Header].=" - $row_part[title]";
}

if(strlen($input["item_title"])){
    $query="select cat_item.*,fname,cat_item_images.id as image_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.seo_alias='".$input["item_title"]."' order by b_code,title asc";
    $result=my_query($query,$conn);
    $row = $result->fetch_array();
    $tags=array_merge($tags,$row);
    if(is_file($IMG_ITEM_PATH.$row[fname]))$tags[default_image]="<img src={$SUBDIR}catalog/image.php?id={$row['image_id']} item_id={$row['id']} border=0 align=left class=cat_item_image_popup>";

    $tags[Header]="$row[title]";
    $tags[nav_str].="<span class=nav_next>$row[title]</span>";

/*	$query="select * from cat_item_images where item_id='".$_GET["view_item"]."' and id<>'$row[default_img]' order by id asc";
    $result=my_query($query,$conn);
    if($result->num_rows){
            $tags[images]="<div class=images>";
            while ($row = $result->fetch_array())if(is_file($IMG_ITEM_PATH.$row[fname])){
                    $tags[images].="<a href={$IMG_ITEM_URL}$row[fname] target=_blank title=\"$row[descr]\"><img src={$IMG_ITEM_URL}$row[fname] border=0></a>";
            }
            $tags[images].="</div>";
    }
*/
    $content.=get_tpl_by_title("cat_item_view",$tags,$result);
    $content.="
    <div class=cat_back>
    <center><a href=catalo".get_cat_part_href($_SESSION["PART_ID"])." class=button> << Назад</a></center>
    </div>
    ";
    $tags[head_inc] = $JQUERY_INC . 
        "<script type=\"text/javascript\" src=\"{$BASE_HREF}include/js/popup.js\"></script>\n".
        "<script type=\"text/javascript\" src=\"{$BASE_HREF}include/js/jquery.waitforimages.min.js\"></script>\n".
        "<script type=\"text/javascript\" src=\"{$BASE_HREF}catalog/catalog.js\"></script>\n";

    echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
    exit;
}

if( (!isset($_POST["search_x"])) && (!isset($_GET["show_all"])) ){
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
            $href=$SUBDIR.get_cat_part_href($row["id"]);
            $content.="
            <div class=cat_part>
                <div class=cat_part_image_border>
                <div class=cat_part_image_table>
                <div class=cat_part_image>
                <a href=\"{$href}\" title=\"$row[title]\">";
                if(is_file($IMG_PART_PATH.$row[img])){
                        $content.="<img src=\"{$IMG_PART_URL}{$row[img]}\" alt=\"$row[title]\" title=\"$row[title]\">";
                }else{
                        $content.="<br>Изображение отсутствует";
                }		
                $content.="</div></div></div>
                <div class=cat_part_title>$row[title]</a></div>\n
            </div>\n";
            if($deep<$max_deep)sub_part($row[id],$deep+1,$max_deep);
        }
    }
    sub_part($_SESSION["PART_ID"],0,0);
    $content.="</div>";
}

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

$query="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id,cat_item.seo_alias from cat_item 
left join cat_item_images on (cat_item_images.id=default_img)"
.(isset($_GET["show_all"])?"":" where part_id='".$_SESSION["PART_ID"]."'")." 
group by cat_item.id order by cat_item.id,b_code,title asc limit $offset,$settings[catalog_items_per_page]";
$result=my_query($query,$conn,1);
if($result->num_rows){
    $content.="<div id=cat_items>\n";
    while ($row = $result->fetch_array()){
        $item_a='<a href="'.$SUBDIR.get_cat_part_href($_SESSION["PART_ID"]).$row['seo_alias'].'">';
        $content.="
        <div class=cat_item>
            <div class=cat_item_title>$row[title]</div>\n
            <div class=cat_item_image_border>
            <div class=cat_item_image_table>
            <div class=cat_item_image item_id='$row[item_id]'>";
            if(is_file($IMG_ITEM_PATH.$row[fname])){
                    $content.="$item_a<img src=\"".$SUBDIR."catalog/image.php?id=".$row[image_id]."\" alt=\"$row[title]\" title=\"$row[title]\"></a>";
            }else{
                    $content.="Изображение отсутствует";
            }		
            $content.="</div></div></div>\n
            <div class=cat_item_descr><i>".nl2br($row['descr'])."</i></div>\n
            <div class=cat_item_price>".($row[price] ? "Цена $row[price] руб./час" : "")."</div>\n
        </div>\n";
    }
    $content.="</div>\n";
}elseif( ($_SESSION["PART_ID"]) && (!$subparts)){
    $content.=my_msg_to_str("list_empty");
}

if($_SESSION["PART_ID"]){
    if (strlen($row_part[descr]))
        $content.="<div class=part_descr>".$row_part['descr']."</div>\n";
    if($subparts){
	$href_id=0;
    }else{
	list($href_id)=my_select_row("select prev_id from cat_part where id='".$_SESSION["PART_ID"]."'", 1);
    }
    $content.="
    <div class=cat_back>
    <center><a href=".$SUBDIR.get_cat_part_href($href_id)." class=button> << Назад</a></center>
    </div>
    ";
}


if($_SESSION["PART_ID"]==0){
    $query = "select title,content from article_item where id='35'";
    $result = my_query($query, $conn);
    list($title, $text) = $result->fetch_array();
    $content=$text."".$content;
}


echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
?>
