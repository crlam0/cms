<?php
if(!isset($input)) {
    require '../../include/common.php';
}

$tags['Header'] = 'Прайс-лист';

$tags['INCLUDE_HEAD'] .= '<link href="' . $SUBDIR . 'css/price.css" type="text/css" rel=stylesheet />' . "\n";
$tags['nav_str'] .= "<span class=nav_next>{$tags['Header']}</span>";
$tags['INCLUDE_JS'] .=  
        '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>'."\n".
        '<script type="text/javascript" src="'.$BASE_HREF.'modules/price/price.js"></script>'."\n";


if (isset($input['add_buy']) && isset($input['cnt'])) {
    $cnt=(int)$input['cnt'];
    if($cnt>0 && $cnt<99) {
        $_SESSION['BUY'][$input['item_id']]['count']+=$cnt;
        echo 'OK';
    } else {
        echo 'ERR';
    }
    exit;
}


function part_items($part_id) {
    $content = '';

    $query = "select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
    left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id)
    where part_id='{$part_id}'
    group by cat_item.id
    order by num,title asc";
    $result = my_query($query, true);
    if ($result->num_rows) {
        $content .= get_tpl_by_title('price_items', [], $result);
    }
    return $content;
}

if (true) {
    $content .= "<div id=cat_parts>";
    $query = "select content from article_item where seo_alias='before_price'";
    list($before_price) = my_select_row($query);
    $content .= $before_price . "<br />";
    $subparts = 0;

    function sub_part($prev_id, $deep, $max_deep) {
        global $content, $subparts;
        if ($deep){
            $subparts++;
        }
        $query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='$prev_id' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result = my_query($query, true);
        while ($row = $result->fetch_array()) {
//          $subparts++;
            if ((!$deep) && (!$prev_id)) {
                $content .= "<div class=root_part><a name={$row['seo_alias']}></a><h3>{$row['title']}</h3></div>";
            } else {
                $content .= "
                <div class=sub_part>
                    <h4>{$row['title']}</h4>                    
                </div>";
            }
            $content .= part_items($row['id']);
            if ($deep < $max_deep){
                sub_part($row['id'], $deep + 1, $max_deep);
            }
        }
    }

    sub_part(0, 0, 2);
    $content .= "</div>";
    $content .= "</div>";
}


$price_parts_content = "";
$query = "SELECT cat_part.*from cat_part where prev_id='0' order by cat_part.num,cat_part.title asc";
$result = my_query($query, true);
while ($row = $result->fetch_array()) {
    $price_parts_content .= "<a href=#{$row['seo_alias']}>{$row['title']}<a><br />";
}

$final_content = '
    <div id="price">
    <div class="col-md-8">' . $content . '</div>
    <div class="col-md-4" id="price_parts">' . $price_parts_content . '</div>
    </div>
    ';

/*
if (strlen($row_part['descr_bottom'])) {
    $content .= "<div class=part_descr>" . nl2br($row_part['descr_bottom']) . "</div>\n";
}
 * 
 */


echo get_tpl_by_title($part['tpl_name'], $tags, '', $final_content);