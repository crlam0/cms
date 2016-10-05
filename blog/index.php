<?php
$tags['Header'] = 'Блог';
$tags['Add_CSS'].=';blog_comments';
include '../include/common.php';
include $INC_DIR . 'lib_comments.php';

$MSG_PER_PAGE = $settings["blog_msg_per_page"];
$TABLE="blog_posts";

if (isset($input["uri"])) {
    $params = explode("#", $input["uri"]);
    $query="select id from {$TABLE} where seo_alias like '".$params[0]."'";    
    $result=my_query($query);
    list($post_id)=$result->fetch_array();
    $input["view_post"] = ( is_numeric($post_id) ? $post_id : $input["view_post"]);

    if(strstr($input["uri"],"page")){
        $_SESSION["BLOG_PAGE"]=str_replace("page","",$input["uri"]);
    }else{
        $_SESSION["BLOG_PAGE"]=1;
    }
}

if(!is_array($input)){
    $_SESSION["BLOG_PAGE"] = 1;
}

$comments = new COMMENTS ("blog",$input["view_post"]);

if ($input["view_post"]) {
    $query = "select {$TABLE}.*,users.fullname as author from {$TABLE} left join users on (users.id=uid) where {$TABLE}.id='{$input["view_post"]}'";
    $result = my_query($query, $conn, true);
    $row =$result->fetch_array();

    $tags[nav_str].="<span class=nav_next><a href=\"{$server["PHP_SELF_DIR"]}\">$tags[Header]</a></span>";
    $tags[nav_str].="<span class=nav_next>{$row["title"]}</span>";
    $tags[Header] .= " - ".$row["title"];
    
    $content.="<div id=blog>";
    $row["post_title"]=$row["title"];
    $row["content"] = replace_base_href($row["content"], false);

    if(strlen($row["target_type"])){
        $href=(strlen($row["href"]) ? $row["href"] : $SUBDIR.get_menu_href(array(),$row) );
        $row["target_link"]="<a href=\"{$href}\" class=button>Перейти >></a>";
    }

    if(is_file($DIR.$settings['blog_img_path'].$row['image_name'])){
        $row["image"]='  
        <div id="featured_image">
            <img width="150" height="150" src="'.$SUBDIR.$settings['blog_img_path'].$row['image_name'].'" class="attachment-150x150 wp-post-image" alt="'.$row['title'].'">    
        </div>';
    }            
    $row["comment_line"] = "Комментариев: " . $comments->show_count($row[id]);
    unset($row['post_title']);
    $content.=get_tpl_by_title("blog_post", $row, $result);
    $content.="</div>";

    $comments->get_form_data($input);
    $content.=$comments->show_list();
    $tags["action"]=$SUBDIR.get_post_href(null,$row)."#comments";
    $content.=$comments->show_form($tags);
    
} else {

    $tags[nav_str].="<span class=nav_next>$tags[Header]</span>";
    
    $query = "SELECT ceiling(count(id)/$MSG_PER_PAGE) from {$TABLE} where active='Y'";
    list($PAGES) = my_select_row($query, NULL, true);
    $offset=$MSG_PER_PAGE*($_SESSION["BLOG_PAGE"]-1);

    if ($PAGES > 1) {
        $tags[pages_list] = "<div class=pages>Страницы: ";
        for ($i = 1; $i <= $PAGES; $i++)
            $tags[pages_list].=($i == $_SESSION["BLOG_PAGE"] ? "[ <b>$i</b> ]&nbsp;" : "[ <a href=" . dirname($server["PHP_SELF"]) . "/page" . $i ."/>$i</a> ]&nbsp;");
        $tags[pages_list].="</div>";
    }

    $query = "SELECT {$TABLE}.*,users.fullname as author,date_format(date_add,'%Y-%m-%dT%H:%i+06:00') as timestamp
        from {$TABLE} left join users on (users.id=uid)
        where {$TABLE}.active='Y'
        group by {$TABLE}.id  order by {$TABLE}.id desc limit $offset,$MSG_PER_PAGE";
    $result = my_query($query, $conn, true);

    if (!$result->num_rows) {
        $content.=my_msg_to_str("part_empty");
    } else {
        $content.="<div id=blog>";
        $content.=$tags[pages_list];
        while ($row = $result->fetch_array()) {
            $row["post_title"]="<a href=\"".$SUBDIR.get_post_href(null,$row)."\" title=\"{$row["title"]}\">".$row["title"]."</a>";
            $row["content"] = replace_base_href($row["content"], false);
            if(strlen($row["target_type"])){
                $href=(strlen($row["href"]) ? $row["href"] : $SUBDIR.get_menu_href(array(),$row) );
                $row["target_link"]="<a href=\"{$href}\" class=button>Перейти >></a>";
            }
            if(is_file($DIR.$settings['blog_img_path'].$row['image_name'])){
                $row["image"]='  
                <div id="featured_image">
                    <img width="150" height="150" src="'.$SUBDIR.$settings['blog_img_path'].$row['image_name'].'" class="attachment-150x150 wp-post-image" alt="'.$row['title'].'">    
                </div>';
            }            
            $row["comment_line"] = 
                    " [ <a href=\"".$SUBDIR.get_post_href(null,$row)."#comment_form\" title=\"{$row["title"]}\">Добавить комментарий</a> ] ".
                    " [ <a href=\"".$SUBDIR.get_post_href(null,$row)."#comments\" title=\"{$row["title"]}\">".
                    "Комментариев: " . $comments->show_count($row[id])."</a> ]";
            $content.=get_tpl_by_title("blog_post", $row, $result);

        }
        $content.=$tags[pages_list];
        $content.="</div>";
    }
}

echo get_tpl_by_title($part[tpl_name], $tags, "", $content);

?>
