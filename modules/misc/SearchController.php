<?php

use classes\App;
use classes\BaseController;

namespace modules\misc;

/**
 * Description of SearchController
 *
 * @author BooT
 */
class SearchController extends BaseController{
    
    public function actionIndex() {
        $this->title = 'Поиск';
        $this->breadcrumbs[] = ['title'=>$this->title]; 
        
        $content='
        <form action="'.App::$SUBDIR.'search/" method="post">
            <input type="edit" maxlength="255" size="48" name="search_str" value="'.App::$input['search_str'].'">
            <input type="submit" value="Искать">    
        </form><br />';

        if(strlen(App::$input['search_str'])>3){
            $query="
            (SELECT id, 'article' as type, seo_alias, title, content, MATCH (title,content) AGAINST ('" . App::$input["search_str"] . "') AS score
            FROM article_item 
            WHERE MATCH (title,content) AGAINST ('" . App::$input["search_str"] . "'))
            UNION    
            (SELECT id, 'news' as type, seo_alias, title, content, MATCH (title,content) AGAINST ('" . App::$input["search_str"] . "') AS score
            FROM blog_posts
            WHERE MATCH (title,content) AGAINST ('" . App::$input["search_str"] . "'))
            order by score desc";
            $result=App::$db->query($query);
            $result_cnt=$result->num_rows;
            if($result_cnt>0){
                $content.="<h5>Найдено {$result_cnt} совпадений.</h5><br />";
                while ($row = $result->fetch_array()) {
                    switch ($row['type']){
                        case "article":
                            $href=App::$SUBDIR . App::$routing->getUrl('article', $row['id']);
                            break;
                        case "news":
                            $href=App::$SUBDIR . App::$routing->getUrl('blog_post', null, $row);
                            break;
                    }
                    $content.="<a class=search_result href=\"{$href}\" title=\"{$row["title"]}\">{$row["title"]}</a><br />";
                    $content_str = strip_tags($row["content"]);
                    $content_str = cut_string($content_str,100);
                    $content.="<span class=search_content>".$content_str."</span><br />";
                }
            }else{
                $content.=App::$message->get('warnig', $tags, 'Ничего не найдено.');
            }
        }else{
            $content.=App::$message->get('warnig', $tags, 'Поисковый запрос слишком короткий.');
        }

        return $content;
    }
}
