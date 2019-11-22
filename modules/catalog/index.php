<?php

if (!isset($input)) {
    require '../../include/common.php';
}
include 'functions.php';
use Classes\Pagination;

$tags['Header'] = isset($settings['catalog_header']) ? $settings['catalog_header'] : 'Магазин';
$tags['INCLUDE_JS'] .= '<script type="text/javascript" src="' . $BASE_HREF . 'modules/catalog/catalog.js"></script>' . "\n";

if (is_array($input) && !count($input)) {
    $input['part_id'] = 0;
}

if (isset($input['uri'])) {
    $params = explode('/', $input['uri']);
    $part_id = 0;
    foreach ($params as $alias) {
        // if(strstr($alias,'page')){
        if(preg_match("/^page\d{1,2}$/", $alias)) {
            $input['page']=str_replace('page','',$alias);
        } else {
            $query = "select id from cat_part where seo_alias like '$alias' and prev_id='{$part_id}'";
            $row = my_select_row($query, true);
            if (is_numeric($row['id'])) {
                $part_id = $row['id'];
            }
        }
    }
    $input['part_id'] = $part_id;
    unset($part_id);
}

$input['view_item'] = null;
$current_part_id = null;

if (isset($input['part_id']) && !isset($input['item_title'])) {
    $current_part_id = $input['part_id'];
    unset($_SESSION['catalog_page']);
}

if (isset($input['item_title'])) {
    $query = "select id,part_id from cat_item where seo_alias = '{$input['item_title']}' and part_id='{$input['part_id']}'";
    $row = my_select_row($query, true);
    if (is_numeric($row['id'])) {
        $input['view_item'] = $row['id'];
        $current_part_id = $row['part_id'];
    }
}

// if ($input['view_item']) {
//     list($current_part_id) = my_select_row("select part_id from cat_item where id='{$input["view_item"]}'", 1);
// }

$IMG_ITEM_PATH = $DIR . $settings['catalog_item_img_path'];
$IMG_ITEM_URL = $BASE_HREF . $settings['catalog_item_img_path'];
$IMG_PART_PATH = $DIR . $settings['catalog_part_img_path'];
$IMG_PART_URL = $BASE_HREF . $settings['catalog_part_img_path'];

if (isset($input['add_buy']) && $input['cnt']) {
    if (!isset($_SESSION['BUY'][$input['item_id']]['count'])) {
        $_SESSION['BUY'][$input['item_id']]['count'] = 0;
    }
    $cnt = (int) $input['cnt'];
    if ($cnt > 0 && $cnt < 99) {
        $_SESSION['BUY'][$input['item_id']]['count'] += $cnt;
        $json['result'] = 'OK';
        $json['count'] = count($_SESSION['BUY']);
    } else {
        $json['result'] = 'ERR';
    }
    echo json_encode($json);
    exit;
}


function prev_part($prev_id, $deep, $arr) {
    $query = "SELECT id,title,prev_id from cat_part where id='$prev_id' order by title asc";
    $result = my_query($query);
    if (!$result->num_rows) {
        return null;
    }
    $arr[$deep] = $result->fetch_array();
    if ($arr[$deep]['prev_id']) {
        $arr = prev_part($arr[$deep]['prev_id'], $deep + 1, $arr);
    }
    return $arr;
}

if ($current_part_id) {
    add_nav_item(isset($settings['catalog_header']) ? $settings['catalog_header'] : 'Магазин', 'catalog/');
    $arr = prev_part($current_part_id, 0, array());
    $arr = array_reverse($arr);
    $max_size = sizeof($arr) - 1;
    $current_part_deep = 0;
    while (list ($n, $row) = @each($arr)) {
        $current_part_deep++;
        if (($n < $max_size) || (strlen($input['item_title']))) {
            add_nav_item($row['title'],get_cat_part_href($row['id']));
            $tags['Header'] .= " - {$row['title']}";
        } else {
            add_nav_item($row['title']);
        }
    }
}

if ($current_part_id) {
    $row_part = my_select_row("select * from cat_part where id='{$current_part_id}'", 1);
    //	if(is_file($IMG_PART_PATH.$row[img]))echo "<img src=$IMG_PART_URL$row[img] border=0 align=left>\n";
    $tags['Header'] = $row_part['title'];
}

/*
 * ====================================================================================
 * 
 * Show popup content.
 * 
 * ====================================================================================
 */

if ($input['get_popup_content']) {
    if(!isset($input['item_id'])){
        echo 'error';
        exit;
    }

    $query = "select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id) where cat_item.id='" . $input["item_id"] . "' order by b_code,title asc";
    $result = my_query($query);
    $row = $result->fetch_array();

    $row['price'] = ($row['price'] ? "Цена: " . $row['price'] . " руб" : "Цена договорная.");

    $tags = array_merge($tags, $row);
    if (isset($row['fname']) && is_file($IMG_ITEM_PATH . $row['fname'])) {
        $tags['default_image'] = "<img src={$IMG_ITEM_URL}{$row['fname']} border=0 align=left>";
    } else {
        $tags['default_image'] = 'Изображение отсутствует.';
    }

    $content = get_tpl_by_name('cat_item_view', $tags, $result);
    $content .= "
            <br>
            <input class=\"cnt_{$input["item_id"]}\" size=1 maxlength=2 value=1>
            <a class=buy_button item_id=\"{$input["item_id"]}\">В корзину</a>
        ";
    $json['title'] = $tags['title'];
    $json['content'] = $content;
    echo json_encode($json);
    exit;
}

if ($input['get_popup_image_content']) {
    
    list($default_img,$default_img_fname,$title)=my_select_row("select default_img,fname,cat_item.title from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='".$input["item_id"]."'",false);
    
    $nav_ins = '';
    
    list($prev_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id<'" . $input["image_id"] . "' and id<>'{$default_img}' order by id desc limit 1", false);
    if ($input["image_id"] != $default_img){
        if ($prev_id){
            $nav_ins.= "<a image_id={$prev_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
        }else{     
            $nav_ins.= "<a image_id={$default_img} item_id={$input["item_id"]} file_name=\"{$default_img_fname}\" class=\"cat_image_button btn btn-default\"><< Предыдущая</a>";
        }
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id>'" . $input["image_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id) {
            $nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
        }
    }else{
        list($next_id,$fname) = my_select_row("select id,fname from cat_item_images where item_id='" . $input["item_id"] . "' and id<>'{$default_img}' order by id asc limit 1", false);
        if ($next_id) {
            $nav_ins.= "<a image_id={$next_id} item_id={$input["item_id"]} file_name={$fname} class=\"cat_image_button btn btn-default\">Следующая >></a>";
        }
    }

    $URL=get_item_image_url($input["file_name"], 500, 0);
    
    $content.="<center><img src=\"{$SUBDIR}{$URL}\" border=0></center>";
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

if ($input['view_item']) {
    $query = "select cat_item.*,fname,cat_item_images.descr as image_descr,cat_item_images.id as cat_item_images_id from cat_item left join cat_item_images on (cat_item_images.id=default_img) where cat_item.id='" . $input['view_item'] . "'";
    $result = my_query($query);
    if ($result->num_rows) {
        $row = $result->fetch_array();
        $tags = array_merge($tags, $row);

        $tags['Header'] = $row['title'];
        add_nav_item($row['title']);

        $query = "select * from cat_item_images where item_id='{$row['id']}' and id<>'{$row['default_img']}' order by id asc";
        $result = my_query($query);
        if ($result->num_rows) {
            $tags['images'] = $result->fetch_all(MYSQLI_ASSOC);
        }
        if(!$related_products = my_json_decode($row_part['related_products'])) {
            $related_products=[];
        }
        if(count($related_products)) {
            $where_str=implode(',',array_keys($related_products));
            $query ="select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
                left join cat_item_images on (cat_item_images.id=default_img)
                where cat_item.id in (" . $where_str . ")
                group by cat_item.num   
                order by cat_item.num,b_code,title asc";
            $result = my_query($query);
            if ($result->num_rows) {
                $tags['related_products'] .= get_tpl_by_name('cat_item_list', $tags, $result);
            }
        }
        if($_SESSION['catalog_page']>1) {
            $tags['page'] = 'page' . $_SESSION['catalog_page'] . '/';
        }
        $content .= get_tpl_by_name('cat_item_view', $tags, $result);        
    } else {
        $content .= my_msg_to_str('notice', [], 'Товар не найден');
    }

    echo get_tpl_default($tags, '', $content);
    exit;
}

/*
 * ====================================================================================
 * 
 * Show catalog parts.
 * 
 * ====================================================================================
 */

$query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='{$input['part_id']}' group by cat_part.id order by cat_part.num,cat_part.title asc";
$result = my_query($query);

if ($result->num_rows) {
    $tags['functions'] = [];
    $content .= get_tpl_by_name('cat_part_list', $tags, $result);
} 

/*
 * ====================================================================================
 * 
 * Show catalog items.
 * 
 * ====================================================================================
 */

if (isset($row_part['descr']) && strlen($row_part['descr'])) {
    $tags['part_descr'] = $row_part['descr'];
}
if (!isset($_SESSION['catalog_page'])){
    $_SESSION['catalog_page'] = 1;
}
if (isset($input['page'])) {
    $_SESSION['catalog_page'] = $input['page'];
}
list($total) = my_select_row("SELECT count(id) from cat_item where part_id='" . $current_part_id . "'", 1);

$pager = new Pagination($total,$_SESSION["catalog_page"],$settings['catalog_items_per_page']);
$tags['pager'] = $pager;

$query = "select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
        left join cat_item_images on (cat_item_images.id=default_img)
        where part_id='" . $current_part_id . "'
        group by cat_item.id   
        order by cat_item.num,b_code,title asc limit {$pager->getOffset()},{$pager->getLimit()}";
$result = my_query($query, true);
if ($result->num_rows) {
    $tags['cat_part_href'] = get_cat_part_href($current_part_id);
    $tags['functions'] = [];
    $content .= get_tpl_by_name('cat_item_list', $tags, $result);
} elseif (($current_part_id) && (!$subparts)) {
    list($tags['image_name'],$tags['title']) = my_select_row("select image_name,title from cat_part where id='{$current_part_id}'");
    $content .= get_tpl_by_name('cat_item_list_empty.html.twig', $tags, $result);
}

if ($current_part_id) {
    list($href_id) = my_select_row("select prev_id from cat_part where id='{$current_part_id}'", true);
    $content .= '
    <div class="cat_back">
    <center><a href="' . $SUBDIR . get_cat_part_href($href_id) . '" class="btn btn-default"> << Назад</a></center>
    </div>
    ';
}

add_nav_item(isset($settings['catalog_header']) ? $settings['catalog_header'] : 'Магазин', null, true);

echo get_tpl_default($tags, '', $content);
