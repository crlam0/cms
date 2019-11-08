<?php
if(!isset($input)) {
    require '../../include/common.php';
}

include 'functions.php';
use Classes\Comments;
use Classes\Pagination;

$tags['Header'] = "Галерея";

$settings['gallery_use_popup']=true;

$view_gallery = null;

if ( (isset($input['uri'])) && (!isset($input['load']))) {
    $params = explode("/", $input['uri']);
    
    $query="select id,title,seo_alias from gallery_list where seo_alias like '".$params[0]."'";
    $current_gallery = my_select_row($query);
    $view_gallery = $current_gallery['id'];
    
    if(isset($params[1]) && strlen($params[1])){
        $gallery_page=$params[1];
    }else{
        $gallery_page=1;
    }
}

if($settings['gallery_use_popup']){
    $tags['INCLUDE_JS'] .= '<script type="text/javascript" src="'.$BASE_HREF.'modules/gallery/gallery.js"></script>'."\n";
}

// $tags['nav_str'].="<a href=" . $SUBDIR . "gallery/ class=nav_next>{$tags['Header']}</a>";

if($input['view_gallery']) {
// if ($input["view_gallery"]) {
    $view_gallery = $input['id'];
}
if(!isset($input) || !($input->count())){
    $view_gallery = "";
    $gallery_page = 1;
}

if (!isset($gallery_page)){
    $gallery_page = 1;
}

if (isset($input["page"])) {
    $gallery_page = $input["page"];
}



if ( (isset($input) && $input['view-image']) && (!$view_gallery) ) {
    list($view_gallery) = my_select_row("select gallery_id from gallery_images where id='{$input['id']}'");
}

if ($input['view_image'] || (isset($input['load']))) {
    $query = "SELECT * from gallery_images where id='{$input['id']}'";
    $row = my_select_row($query, true);
    $tags = array_merge($row, $tags);
    $gallery_id = $row['gallery_id'];

    list($tags['prev_id']) = my_select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id<'{$tags['id']}' order by id desc limit 1", true);
    list($tags['next_id']) = my_select_row("select id from gallery_images where gallery_id='{$gallery_id}' and id>'{$tags['id']}' order by id asc limit 1", true);
    if ($input['view_image']){
        $content = get_tpl_by_title('gallery_image_view', $tags);
        echo get_tpl_default($tags, '', $content);
        exit();
    }
    
    $file_name = $DIR . $settings['gallery_upload_path'] . $row['file_name'];
    if ($file_name) {
        $cache_file_name = gallery_get_cache_file_name($file_name, gallery_get_max_width());
        if(is_file($DIR . $cache_file_name)) {
            $tags['URL']=$cache_file_name;
        } else {
            $tags['URL']="modules/gallery/image.php?id={$tags['id']}&clientHeight=".$input['clientHeight'];
        }
    }
    $json=$tags;
    $json['content'] = get_tpl_by_title('gallery_image_view', $tags);
    echo json_encode($json);
    exit();
}


if (($view_gallery)||($input['page'])) {
    $tags['Header'] = $current_gallery['title'];
    
    $tags['nav_str'].='<span class="nav_next"><a href="gallery/">Галерея</a></span>';
    $tags['nav_str'].='<span class="nav_next">'.$tags['Header'].'</span>';
    add_nav_item('Галерея', 'gallery/');
    add_nav_item($tags['Header']);    
    
    list($total) = my_select_row("SELECT count(id) from gallery_images where gallery_id='{$view_gallery}'", false);
    $pager = new Pagination($total,$gallery_page,$settings['gallery_images_per_page']);
    $tags['pager'] = $pager;
    $tags['gallery_list_href'] = get_gallery_list_href($view_gallery);
    $list_href = 'gallery/'.$current_gallery['seo_alias'].'/';
    
    $query = "SELECT * from gallery_images where gallery_id=" . $view_gallery . " order by id asc limit {$pager->getOffset()},{$pager->getLimit()}";
    $result = my_query($query, false);
    if (!$result->num_rows) {
        $content = my_msg_to_str('list_empty', $tags, '');
    } else {
        $content = get_tpl_by_title('gallery_images_list', $tags, $result);
    }
    
    if($settings['gallery_use_comments']) {
        $comments = new Comments ('gallery',$view_gallery);
        $comments->get_form_data($input);
        $content.=$comments->show_list();
        $tags["action"]=$SUBDIR.$list_href."#comments";
        $content.=$comments->show_form($tags);
    }
    
    echo get_tpl_default($tags, '', $content);
    exit();
}

$query = "SELECT 
        gallery_list.*,count(images.id) AS images,max(images.date_add) AS last_images_date_add,
        def_img.id as def_id,def_img.file_name as def_file_name
    FROM gallery_list
    LEFT JOIN gallery_images AS images ON (images.gallery_id=gallery_list.id)
    LEFT JOIN gallery_images AS def_img ON (def_img.id=default_image_id)
    WHERE gallery_list.active='Y'
    GROUP BY gallery_list.id ORDER BY last_images_date_add DESC,gallery_list.date_add DESC";

$result = my_query($query, true);
if (!$result->num_rows) {
    $content = my_msg_to_str('part_empty');
} else {
    $content = get_tpl_by_title('gallery_part_list', $tags, $result);
}

add_nav_item('Галерея');

echo get_tpl_default($tags, '', $content);


