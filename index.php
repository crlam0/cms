<?php
$tags['Add_CSS'].=';blog_comments';
include 'include/common.php';

if ($input['error']) {
    $tags['Header'] = 'Ошибка 404';
    $tags['file_name'] = $server['REQUEST_URI'];
    $content = my_msg_to_str('file_not_found', $tags, "");

    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit();
}

$query="select title,content from article_item where seo_alias='main'";
$result=my_query($query);
list($title,$text)=$result->fetch_array();

$tags[Header]=$title;
$text=replace_base_href($text);

$content="<table width=100% border=0 cellspacing=0 cellpadding=0 align=center>
<tr class=content><td>".$text."<br></TD></TR>
</table><br>";

include $INC_DIR . 'lib_comments.php';
$comments = new COMMENTS ("blog");

$TABLE='blog_posts';
$query = "SELECT {$TABLE}.*,users.fullname as author from {$TABLE} left join users on (users.id=uid)
        where {$TABLE}.active='Y'
        group by {$TABLE}.id  order by {$TABLE}.id desc limit {$settings['blog_msg_per_page']}";
$result = my_query($query, $conn, true);

if ($result->num_rows) {
    $content.='<div id=blog>';
    while ($row = $result->fetch_array()) {
            $row['post_title']="<a href=\"".$SUBDIR.get_post_href(null,$row)."\" title=\"{$row["title"]}\">".$row["title"]."</a>";
            $row['content'] = replace_base_href($row["content"], false);
            if(strlen($row["target_type"])){
                $href=(strlen($row["href"]) ? $row["href"] : $SUBDIR.get_menu_href(array(),$row) );
                $row["target_link"]="<br><a href=\"{$href}\">Перейти >></a>";
            }
            if(is_file($DIR.$settings['blog_img_path'].$row['image_name'])){
                $row["image"]='  
                <div id="featured_image">
                    <img width="150" height="150" src="'.$SUBDIR.$settings['blog_img_path'].$row['image_name'].'" class="attachment-150x150 wp-post-image" alt="'.$row['title'].'">    
                </div>';
            }            
            $row['comment_line'] = 
                    " [ <a href=\"".get_post_href(null,$row)."#comment_form\" title=\"{$row["title"]}\">Добавить комментарий</a> ] ".
                    " [ <a href=\"".get_post_href(null,$row)."#comments\" title=\"{$row["title"]}\">".
                    "Комментариев: " . $comments->show_count($row[id])."</a> ]";

            $content.=get_tpl_by_title('blog_post', $row, $result);

    }
    $content.='<center><a href=blog/page2/>Далее >></a></center>';
    $content.='</div>';
}

echo get_tpl_by_title($part[tpl_name],$tags,"",$content);

