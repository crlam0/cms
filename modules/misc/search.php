<?php
$tags['Header']="Поиск";

$content='
<form action="'.$SUBDIR.'search/" method="post">
    <input type="edit" maxlength="255" size="48" name="search_str" value="'.$input["search_str"].'">
    <input type="submit" value="Искать">    
</form><br />';

if(strlen($input['search_str'])>3){
    $query="
    (SELECT id, 'article' as type, seo_alias, title, content, MATCH (title,content) AGAINST ('{$input["search_str"]}') AS score
    FROM article_item 
    WHERE MATCH (title,content) AGAINST ('{$input["search_str"]}'))
    UNION    
    (SELECT id, 'news' as type, seo_alias, title, content, MATCH (title,content) AGAINST ('{$input["search_str"]}') AS score
    FROM blog_posts
    WHERE MATCH (title,content) AGAINST ('{$input["search_str"]}'))
    order by score desc";
    $result=my_query($query);
    $result_cnt=$result->num_rows;
    if($result_cnt>0){
        $content.="<h5>Найдено {$result_cnt} совпадений.</h5><br />";
        while ($row = $result->fetch_array()) {
            switch ($row["type"]){
                case "article":
                    $href=$SUBDIR.get_article_href($row["id"]);
                    break;
                case "news":
                    $href=$SUBDIR.get_post_href(null,$row);
                    break;
            }
            $content.="<a class=search_result href=\"{$href}\" title=\"{$row["title"]}\">{$row["title"]}</a><br />";
            $content_str = strip_tags($row["content"]);
            $content_str = cut_string($content_str,100);
            $content.="<span class=search_content>".$content_str."</span><br />";
        }
    }else{
        $content.=my_msg_to_str('warnig', $tags, 'Ничего не найдено.');
    }
}else{
    $content.=my_msg_to_str('warnig', $tags, 'Поисковый запрос слишком короткий.');
}

echo get_tpl_by_title($part['tpl_name'],$tags,'',$content);
