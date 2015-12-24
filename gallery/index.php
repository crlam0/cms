<?php
$tags[Header] = "Галерея";
include "../include/common.php";
include $INC_DIR . "lib_comments.php";

if ( (isset($input["uri"])) && (!isset($input["load"]))) {
    $params = explode("/", $input["uri"]);
    
    $query="select id from gallery_list where seo_alias like '".$params[0]."'";
    $result=my_query($query);
    list($_SESSION["view_gallery"])=$result->fetch_array();
    
    if(strlen($params[1])){
        $_SESSION["gallery_page"]=$params[1];
    }else{
        $_SESSION["gallery_page"]=1;
    }
}

if($settings["gallery_use_popup"]){
    $tags[head_inc] .= $JQUERY_INC .
            '<script type="text/javascript" src="'.$BASE_HREF.'include/js/popup.js"></script>'."\n".
            '<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.waitforimages.min.js"></script>'."\n".
            '<script type="text/javascript" src="'.$BASE_HREF.'gallery/gallery.js"></script>'."\n";
}

$tags[nav_str].="<a href=" . $server["PHP_SELF_DIR"] . " class=nav_next>$tags[Header]</a>";

if ($input["view_gallery"]) {
    $_SESSION["view_gallery"] = $input["id"];
}
if(!is_array($input)){
    $_SESSION["view_gallery"] = "";
    $_SESSION["gallery_page"] = 1;
}

if (!isset($_SESSION["gallery_page"]))$_SESSION["gallery_page"] = 1;

if (isset($input["page"])) {
    $_SESSION["gallery_page"] = $input["page"];
}

function show_img($tmp, $row) {
    global $DIR, $settings, $SUBDIR;
    if (is_file($DIR . $settings["gallery_upload_path"] . $row[file_name])) {
        $content="";
        if($settings["gallery_use_popup"]==true){
            $content.="
                <img src=\"" . $SUBDIR . "gallery/image.php?preview=1&id=$row[id]\" border=0 item_id=$row[id] class=gallery_popup alt=\"$row[title]\">
                ";
        }else{
            $content.="
                <a href=" . $server["PHP_SELF"] . "?view_image=1&id=$row[id] title=\"$row[title]\">
                <img src=\"" . $SUBDIR . "gallery/image.php?preview=1&id=$row[id]\" border=0 class=preview alt=\"$row[title]\">
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
            <img src=\"" . $SUBDIR . $settings['gallery_list_img_path'] . $row[image_name] . "\" border=0 item_id=$row[id] class=gallery_list_image alt=\"$row[title]\">
            ";
    } else {
        $content = "<div class=empty_img>Изображение отсутствует</div>";
    }
 */
    list($image_id) = my_select_row("select default_image_id from gallery_list where id='{$row["id"]}'", false);
    $row_image = my_select_row("select * from gallery_image where id='{$image_id}'", false);
    if (is_file($DIR . $settings["gallery_upload_path"] . $row_image["file_name"])) {
        $content="
                <img src=\"" . $SUBDIR . "gallery/image.php?preview=1&id={$row_image["id"]}\" border=0 alt=\"$row[title]\">
                ";
    } else {
        $content = "<div class=empty_img>Изображение отсутствует: {$row['file_name']}</div>";
    }
    return $content;
}

if (($input["view_image"]) && (!$_SESSION["view_gallery"])) {
    list($_SESSION["view_gallery"]) = my_select_row("select gallery_id from gallery_image where id='$input[id]'");
}

if (($input["view_image"]) || (isset($input["load"]))) {
    $query = "SELECT * from gallery_image where id='$input[id]'";
    $row = my_select_row($query, true);
    $tags = array_merge($row, $tags);
    $tags[Header] = $row[title];
    $tags[gallery_id] = $_SESSION["view_gallery"];

    list($title) = my_select_row("select title from gallery_list where id=" . $_SESSION["view_gallery"], 1);
    $tags["back_url"]=$server["PHP_SELF"] . "?view_gallery1&id=".$_SESSION["view_gallery"];
    $tags[nav_str].="<a href=" . $tags["back_url"] ." class=nav_next>$title</a><span class=nav_next>$row[title]</span>";

    list($prev_id) = my_select_row("select id from gallery_image where gallery_id=" . $_SESSION["view_gallery"] . " and date_add<'$tags[date_add]' order by date_add desc limit 1", true);
    if ($prev_id)$tags[prev] = "<a href={$server['PHP_SELF']}?view_image=1&id=$prev_id class=button><< Предыдущая</a>";

    list($next_id) = my_select_row("select id from gallery_image where gallery_id=" . $_SESSION["view_gallery"] . " and date_add>'$tags[date_add]' order by date_add asc limit 1", true);
    if ($next_id)$tags[next] = "<a href={$server['PHP_SELF']}?view_image=1&id=$next_id class=button>Следующая >></a>";

    if ($input["view_image"]){
        $content = get_tpl_by_title("gallery_image_view", $tags);
        echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
        exit();
    }
}

if (isset($input["load"])) {
    if($prev_id)$prev_add="<a class=gallery_button item_id=$prev_id><< Предыдущая</a>";
    if($next_id)$next_add="<a class=gallery_button item_id=$next_id>Следующая >></a>";
    echo "
	<center>
        <div id=gallery>
        <div class=title>$tags[title]</div>
        <br>
        <div class=view_image>
        <img src=\"{$SUBDIR}gallery/image.php?id={$tags['id']}&windowHeight={$input['windowHeight']}\" border=0 id=popup_image>
        </div>
        <div class=descr>{$tags['descr']}</div><div class=date>Добавлена {$tags['date_add']}, просмотров {$tags['view_count']}</div>
        <br>
        <div align=center>$prev_add $next_add</div>
        </div></center>
    ";
    exit();
}


if (($_SESSION['view_gallery'])||($input['page'])) {
    list($PAGES) = my_select_row("SELECT ceiling(count(id)/{$settings['gallery_images_per_page']}) from gallery_image where gallery_id=" . $_SESSION['view_gallery'], 0);
    list($title) = my_select_row("select title from gallery_list where id=" . $_SESSION['view_gallery'], 0);
    $tags[Header] = $title;
    $tags[nav_str].="<span class=nav_next>$title</span>";
    if ($PAGES > 1) {
        $tags[pages_list] = "<center>";
        for ($i = 1; $i <= $PAGES; $i++){
            if($i == $_SESSION['gallery_page']) {
                $tags['pages_list'].= "[ <b>$i</b> ]&nbsp;";
            }else{
                $tags['pages_list'].= "[ <a href=" . $SUBDIR . get_gallery_list_href($_SESSION["view_gallery"]) . "$i/>$i</a> ]&nbsp;";
            }
        }    
        $tags[pages_list].="</center><br>";
    }
    $offset = $settings['gallery_images_per_page'] * ($_SESSION['gallery_page'] - 1);
    $query = "SELECT * from gallery_image where gallery_id=" . $_SESSION["view_gallery"] . " order by date_add asc limit {$offset},{$settings['gallery_images_per_page']}";
    $result = my_query($query, $conn, false);
    if (!$result->num_rows) {
        $content = my_msg_to_str("list_empty", $tags, "");
    } else {
        $content = get_tpl_by_title("gallery_images_table", $tags, $result);
    }
    
    $comments = new COMMENTS ("gallery",$_SESSION["view_gallery"]);
    
    $comments->get_form_data($input);
    $content.=$comments->show_list();
    $tags["action"]=$SUBDIR.get_gallery_list_href($_SESSION["view_gallery"])."#comments";
    $content.=$comments->show_form($tags);    
    
    echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
    exit();
}

$query = "SELECT gallery_list.*,count(gallery_image.id) as images,max(gallery_image.date_add) as last_images_date_add
from gallery_list
left join gallery_image on (gallery_image.gallery_id=gallery_list.id)
where gallery_list.active='Y'
group by gallery_list.id order by last_images_date_add desc,gallery_list.date_add desc";
$result = my_query($query, $conn, true);
if (!$result->num_rows) {
    $content = my_msg_to_str("part_empty");
} else {
    $content = get_tpl_by_title("gallery_list_table", $tags, $result);
}
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>
