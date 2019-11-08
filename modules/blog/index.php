<?php
if(!isset($input)) {
    require '../../include/common.php';
}
$tags['Header'] = 'Блог';
$tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.'css/blog_comments.css" type="text/css" rel=stylesheet />'."\n";

use Classes\Comments;
use Classes\Pagination;

$MSG_PER_PAGE = $settings['blog_msg_per_page'];
$TABLE='blog_posts';

$content = '';

if (isset($input['uri'])) {
    if(strstr($input['uri'],'page')){
        $page_num=str_replace('page','',$input['uri']);
        $blog_page=is_integer($page_num) ? $page_num : 1;
    }else{
        $blog_page=1;
        $input['view_post'] = get_id_by_alias($TABLE, $input['uri'], true);
    }
}

if(!$input->count()){
    $blog_page = 1;
}

if(!isset($blog_page)) {
    $blog_page = 1;
}

if(isset($input['view_post'])) {
    $comments = new Comments ('blog',$input['view_post']);
} else {
    $comments = new Comments ('blog', null);
}

function get_post_content ($row) {
    global $SUBDIR;
    
    $content = replace_base_href($row['content'], false);
    $content = preg_replace('/height: \d+px;/', 'max-width: 100%;', $content);
    
    if(strlen($row['target_type'])){
        $href=(strlen($row['target_type'] == 'link') ? $row['href'] : $SUBDIR.get_menu_href(array(),$row) );
        $content.="<br><a href=\"{$href}\">Перейти >></a>";
    }
    return $content;
}

function get_post_comments_count ($row) {
    global $comments;
    return $comments->show_count($row['id']);
}

if($input->count() && $input['view_post']) {
    $query = "select {$TABLE}.*,users.fullname as author from {$TABLE} left join users on (users.id=uid) where {$TABLE}.id='{$input["view_post"]}'";
    $result = my_query($query, true);
    $row = $result->fetch_array();
    $result->data_seek(0);

    $tags['nav_str'].="<span class=nav_next><a href=\"{$SUBDIR}blog/\">{$tags['Header']}</a></span>";
    $tags['nav_str'].="<span class=nav_next>{$row['title']}</span>";
    
    add_nav_item($tags['Header'],'blog/');
    add_nav_item($row['title']);

    $tags['Header'] .= " - ".$row['title'];
       
    $tags['functions'] = ['get_post_href', 'get_post_content', 'get_post_comments_count'];
    $content.=get_tpl_by_name('blog_posts', $tags, $result);

    $comments->get_form_data($input);
    $content.=$comments->show_list();
    $tags['action']=$SUBDIR.get_post_href(null,$row).'#comments';
    $content.=$comments->show_form($tags);
    $content.='<center><a href="'.$SUBDIR.'blog/" class="btn btn-default"> << Назад</a></center>';
} else {

    $tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";
    add_nav_item($tags['Header']);
    
    $query = "SELECT count(id) from {$TABLE} where active='Y'";
    list($total) = my_select_row($query, false);

    $pager = new Pagination($total,$blog_page,$MSG_PER_PAGE);
    $tags['pager'] = $pager;

    $query = "SELECT {$TABLE}.*,users.fullname as author,date_format(date_add,'%Y-%m-%dT%H:%i+06:00') as timestamp
        from {$TABLE} left join users on (users.id=uid)
        where {$TABLE}.active='Y'
        group by {$TABLE}.id  order by {$TABLE}.id desc limit {$pager->getOffset()},{$pager->getLimit()}";
    $result = my_query($query, false);
    
    if (!$result->num_rows) {
        $content.=my_msg_to_str("part_empty");
    } else {    
        $tags['functions'] = ['get_post_href', 'get_post_content', 'get_post_comments_count'];
        $content.=get_tpl_by_name('blog_posts', $tags, $result);
    }
}

echo get_tpl_default($tags, null, $content);


