<?php
$tags['Header']='Каталог услуг';
$tags['Add_CSS'].=';catalog;price';
$tags['INCLUDE_HEAD'].='<link href="'.$SUBDIR.'css/catalog.css" type="text/css" rel=stylesheet />'."\n";;
$tags['INCLUDE_HEAD'].='<link href="'.$SUBDIR.'css/price.css" type="text/css" rel=stylesheet />'."\n";;

include '../include/common.php';

if (!count($input)){
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


if (isset($input[part_id])) {
    $current_part_id = $input[part_id];
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

if (isset($input['add_buy'])) {
    $_SESSION['BUY'][$input['item_id']]['count']+=$input['cnt'];
    echo 'OK';
    exit;
}

function show_img($tmp, $row) {
    global $IMG_ITEM_PATH, $IMG_ITEM_URL;
    if (is_file($IMG_ITEM_PATH . $row[fname])) {
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
 * Show catalog item.
 * 
 * ====================================================================================
 */
if(isset($input['view_item'])){
    $query="select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='".$input['view_item']."' order by b_code,title asc";
    $result=my_query($query);
    $row = $result->fetch_array();
    $tags=array_merge($tags,$row);
    if(is_file($IMG_ITEM_PATH.$row['fname'])){
        $tags['default_image']="<img src={$IMG_ITEM_URL}{$row['fname']} border=0 align=left class=item>";
    } else {
        $tags['default_image'] = 'Изображение отсутствует';
    }    
    $tags['Header']=$row['title'];

    $query="select * from cat_item_images where item_id='{$input['view_item']}' and id<>'{$row['default_img']}' order by id asc";
    $result=my_query($query);
    if($result->num_rows){
        $tags[images]="<div class=images>";
        while ($row = $result->fetch_array()){
            if(is_file($IMG_ITEM_PATH.$row['fname'])){
                $tags[images].="<a href={$IMG_ITEM_URL}{$row['fname']} target=_blank title=\"{$row['descr']}\"><img src={$IMG_ITEM_URL}{$row['fname']} border=0></a>";
            }    
        }
        $tags['images'].="</div>";
    }
    $content.=get_tpl_by_title('cat_item_detail_view',$tags,$result);
    
    list($href_id)=my_select_row("select prev_id from cat_part where id='".$row['part_id']."'", true);
    $content.="
    <div class=cat_back>
    <center><a href=".$SUBDIR.get_cat_part_href($href_id)." class=button> << Назад</a></center>
    </div>
    ";
    echo get_tpl_by_title($part['tpl_name'],$tags,'',$content);
    exit;
}
/* 
 * ====================================================================================
 * 
 * Show catalog parts.
 * 
 * ====================================================================================
 */

if($current_part_deep<=1){
    $query="SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='{$current_part_id}' group by cat_part.id order by cat_part.num,cat_part.title asc";
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
    }
} elseif($current_part_deep > 1) {

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
            if($deep === 0){
                $content .= "<center><h2>{$row['title']}</h2></center>";
            } else {
                $add_tr = "<tr class=header><td colspan=3 style=\"background-color: #ccc;\"><h4>{$row['title']}</h4></td></tr>";
            }
            $content .= get_items_table($row['id'],$add_tr);
            sub_part($row[id],$deep+1,$max_deep);
        }
    }
    sub_part($current_part_id,0,0);
    $content.="</div>";
}

// echo $deep . '   '.$subparts;

/* 
 * ====================================================================================
 * 
 * Get content of items table
 * 
 * ====================================================================================
 */
function price_content($tmp,$row){
    global $row_part;
    $content = "<td align=center>{$row['price']}</td>";
    if ($row_part['price_cnt'] >= 2){ $content .= "<td align=center>{$row['price2']}</td>"; }
    if ($row_part['price_cnt'] >= 3){ $content .= "<td align=center>{$row['price3']}</td>"; }
    if ($row_part['price_cnt'] >= 4){ $content .= "<td align=center>{$row['price4']}</td>"; }
    if ($row_part['price_cnt'] >= 5){ $content .= "<td align=center>{$row['price5']}</td>"; }
    return $content;
}
function price_title($tmp,$row){
    global $SUBDIR;
    if(strlen($row['descr_full'])){
        // return $row['title'] . 'rerere';
        $href=$SUBDIR.get_cat_part_href($row['part_id']).''.$row['seo_alias'];
        return "<a href={$href}>{$row['title']}</a>";
    }  else {
        return $row['title'];
    }
}

function get_items_table ($part_id, $add_tr = '') {
    $query="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id,cat_item.seo_alias from cat_item 
    left join cat_item_images on (cat_item_images.id=default_img)
    where part_id='{$part_id}'
    group by cat_item.id order by num,title,cat_item.id asc ";
    $result=my_query($query, null, true);
    if($result->num_rows){    
        $row_part = my_select_row("select * from cat_part where id='{$part_id}'", true);
        $tags['price_header']="<td width=10%>{$row_part['price1_title']}</td>";
        if ($row_part['price_cnt'] >= 2){ $tags['price_header'].="<td width=10%>{$row_part['price2_title']}</td>"; }    
        if ($row_part['price_cnt'] >= 3){ $tags['price_header'].="<td width=10%>{$row_part['price3_title']}</td>"; }    
        if ($row_part['price_cnt'] >= 4){ $tags['price_header'].="<td width=10%>{$row_part['price4_title']}</td>"; }    
        if ($row_part['price_cnt'] >= 5){ $tags['price_header'].="<td width=10%>{$row_part['price5_title']}</td>"; }    

        $tags['add_tr']=$add_tr;
        $content .= get_tpl_by_title('cat_item_table', $tags, $result);
    }else{
        return null;
    }
    return $content;
}

$content .= get_items_table($current_part_id);

if($current_part_id){
    if (strlen($row_part[descr])){
        $content.="<div class=part_descr>".$row_part['descr']."</div>\n";
    }
    list($href_id)=my_select_row("select prev_id from cat_part where id='{$current_part_id}'", true);
    $content.="
    <div class=cat_back>
    <center><a href=".$SUBDIR.get_cat_part_href($href_id)." class=\"btn btn-default\"> << Назад</a></center>
    </div>
    ";
}


echo get_tpl_by_title($part[tpl_name],$tags,"",$content);
