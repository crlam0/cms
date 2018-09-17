<?php

@include_once "../../include/common.php";

use Classes\Comments;

$tags['Header'] = "Галерея";
$tags['INCLUDE_HEAD'].='<link href="'.$SUBDIR.'css/gallery.css" type="text/css" rel=stylesheet />'."\n";
$tags['INCLUDE_HEAD'].='<link href="'.$SUBDIR.'css/blog_comments.css" type="text/css" rel=stylesheet />'."\n";

$settings["gallery_use_popup"]=true;

$view_gallery = null;

if ( (isset($input['uri'])) && (!isset($input['load']))) {
    $params = explode("/", $input['uri']);
    
    $query="select id from gallery_list where seo_alias like '".$params[0]."'";
    $result=my_query($query);
    list($view_gallery)=$result->fetch_array();
    
    if(isset($params[1]) && strlen($params[1])){
        $gallery_page=$params[1];
    }else{
        $gallery_page=1;
    }
}

if($settings["gallery_use_popup"]){
    $tags['INCLUDE_JS'] .= 
            '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>'."\n".
            '<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.waitforimages.min.js"></script>'."\n".
            '<script type="text/javascript" src="'.$BASE_HREF.'modules/gallery/gallery.js"></script>'."\n";
}

$tags['nav_str'].="<a href=" . $server["PHP_SELF_DIR"] . " class=nav_next>{$tags['Header']}</a>";

if(isset($input) && array_key_exists('view_gallery',$input)) {
// if ($input["view_gallery"]) {
    $view_gallery = $input["id"];
}
if(!isset($input) || !($input->count())){
    $view_gallery = "";
    $gallery_page = 1;
}

if (!isset($gallery_page))$gallery_page = 1;

if (isset($input["page"])) {
    $gallery_page = $input["page"];
}

function show_img($tmp, $row) {
    global $DIR, $settings, $SUBDIR, $server;
    if (is_file($DIR . $settings["gallery_upload_path"] . $row['file_name'])) {
        $content="";
        if($settings["gallery_use_popup"]==true){
            $content.="
                <img src=\"" . $SUBDIR . "modules/gallery/image.php?preview=1&id={$row['id']}\" border=0 item_id={$row['id']} class=gallery_popup alt=\"{$row['title']}\">
                ";
        }else{
            $content.="
                <a href=" . $server["PHP_SELF"] . "?view_image=1&id={$row['id']} title=\"{$row['title']}\">
                <img src=\"" . $SUBDIR . "modules/gallery/image.php?preview=1&id={$row['id']}\" border=0 class=preview alt=\"{$row['title']}\">
                </a>";
        }    
    } else {
        $content = "<div class=empty_img>Изображение отсутствует: {$row['file_name']}</div>";
    }
    return $content;
}

function show_list_img($tmp, $row) {
    global $DIR, $settings, $SUBDIR;
/*    $content="";
    if (is_file($DIR . $settings['gallery_list_img_path'] . $row[image_name])) {
        $content.="
            <img src=\"" . $SUBDIR . $settings['gallery_list_img_path'] . $row[image_name] . "\" border=0 item_id={$row['id']} class=gallery_list_image alt=\"$row[title]\">
            ";
    } else {
        $content = "<div class=empty_img>Изображение отсутствует</div>";
    }
 */
    list($image_id) = my_select_row("select default_image_id from gallery_list where id='{$row["id"]}'", false);
    $row_image = my_select_row("select * from gallery_images where id='{$image_id}'", false);
    if (is_file($DIR . $settings["gallery_upload_path"] . $row_image["file_name"])) {
        $content="
                <img src=\"" . $SUBDIR . "modules/gallery/image.php?preview=1&id={$row_image["id"]}\" border=0 alt=\"{$row['title']}\">
                ";
    } else {
        $content = "<div class=empty_img>Изображение отсутствует: {$row['file_name']}</div>";
    }
    return $content;
}

if ( (isset($input) && $input['view-image']) && (!$view_gallery) ) {
    list($view_gallery) = my_select_row("select gallery_id from gallery_images where id='{$input['id']}'");
}

if ($input['view_image'] || (isset($input['load']))) {
    add_to_debug($input['id']);
    $query = "SELECT * from gallery_images where id='{$input['id']}'";
    $row = my_select_row($query, true);
    $tags = array_merge($row, $tags);
    $tags['Header'] = $row['title'];
    $tags['gallery_id'] = $row['gallery_id'];
    $gallery_id = $row['gallery_id'];

    list($title) = my_select_row("select title from gallery_list where id='{$gallery_id}'", true);
    $tags["back_url"]=$server["PHP_SELF"] . "?view_gallery1&id=".$gallery_id;
    $tags['nav_str'].="<a href=" . $tags['back_url'] ." class=nav_next>$title</a><span class=nav_next>{$row['title']}</span>";

    list($prev_id) = my_select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id<'{$tags['id']}' order by id desc limit 1", true);
    if ($prev_id){
        $tags['prev'] = "<a href={$server['PHP_SELF']}?view_image=1&id={$prev_id} class=button><< Предыдущая</a>";
    }
    list($next_id) = my_select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id>'{$tags['id']}' order by id asc limit 1", true);
    if ($next_id){
        $tags['next'] = "<a href={$server['PHP_SELF']}?view_image=1&id={$next_id} class=button>Следующая >></a>";
    }
    if ($input['view_image']){
        $content = get_tpl_by_title('gallery_image_view', $tags);
        echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
        exit();
    }
}

if (isset($input['load'])) {
    $prev_add='';
    $next_add='';
    
    if($prev_id) {
        $prev_add='<a class="btn btn-default gallery_button" item_id="'. $prev_id. '"><< Предыдущая</a>';
    }
    if($next_id) {
        $next_add='<a class="btn btn-default gallery_button" item_id="'. $next_id. '">Следующая >></a>';
    }
    echo "
	<center>
        <div id=gallery>
        <div class=title>$tags[title]</div>
        <br>
        <div class=view_image>
        <img src=\"{$SUBDIR}modules/gallery/image.php?id={$tags['id']}&clientHeight=".$input['clientHeight']."\" border=0 id=popup_image>
        </div>
        <div class=descr>{$tags['descr']}</div><div class=date>Добавлена {$tags['date_add']}, просмотров {$tags['view_count']}</div>
        <br>
        <div align=center>{$prev_add} {$next_add}</div>
        </div></center>
    ";
    exit();
}


if (($view_gallery)||($input['page'])) {
    list($PAGES) = my_select_row("SELECT ceiling(count(id)/{$settings['gallery_images_per_page']}) from gallery_images where gallery_id=" . $view_gallery, false);
    list($title) = my_select_row("select title from gallery_list where id=" . $view_gallery, false);
    $tags['Header'] = $title;
    $tags['nav_str'].="<span class=nav_next>$title</span>";
    if ($PAGES > 1) {
        $tags['pages_list'] = "<center>";
        for ($i = 1; $i <= $PAGES; $i++){
            if($i == $gallery_page) {
                $tags['pages_list'].= "[ <b>$i</b> ]&nbsp;";
            }else{
                $tags['pages_list'].= "[ <a href=" . $SUBDIR . get_gallery_list_href($view_gallery) . "$i/>$i</a> ]&nbsp;";
            }
        }    
        $tags['pages_list'].="</center><br>";
    }
    $offset = $settings['gallery_images_per_page'] * ($gallery_page - 1);
    $query = "SELECT * from gallery_images where gallery_id=" . $view_gallery . " order by id asc limit {$offset},{$settings['gallery_images_per_page']}";
    $result = my_query($query, false);
    if (!$result->num_rows) {
        $content = my_msg_to_str('list_empty', $tags, "");
    } else {
        $content = get_tpl_by_title('gallery_images_table', $tags, $result);
    }
    
    if($settings['gallery_use_comments']) {
        $comments = new Comments ('gallery',$view_gallery);
        $comments->get_form_data($input);
        $content.=$comments->show_list();
        $tags["action"]=$SUBDIR.get_gallery_list_href($view_gallery)."#comments";
        $content.=$comments->show_form($tags);
    }
    
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

function get_icons($tmp,$input_row){
    global $DIR, $settings, $SUBDIR;
    $content="";
    $query="select * from gallery_images where gallery_id='{$input_row['id']}' limit 6";
    $result = my_query($query, true);
    while($row=  $result->fetch_array()){
        if (is_file($DIR . $settings['gallery_upload_path'] . $row['file_name'])) {
            $content.='<img src="' . $SUBDIR . 'modules/gallery/image.php?icon=1&id='.$row['id'].'" class="list_icon" border="0" alt="'.$row['title'].'" />';
        }
    }
    return $content;
}

$query = "SELECT gallery_list.*,count(gallery_images.id) as images,max(gallery_images.date_add) as last_images_date_add
from gallery_list
left join gallery_images on (gallery_images.gallery_id=gallery_list.id)
where gallery_list.active='Y'
group by gallery_list.id order by last_images_date_add desc,gallery_list.date_add desc";
$result = my_query($query, true);
if (!$result->num_rows) {
    $content = my_msg_to_str("part_empty");
} else {
    $content = get_tpl_by_title("gallery_list_table", $tags, $result);
}
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);

