<?php

namespace modules\misc;

use classes\App;
use classes\BaseController;

/**
 * Description of SearchController
 *
 * @author BooT
 */
class SearchController extends BaseController
{

    public function actionIndex()
    {
        $this->title = 'Поиск';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $result = null;
        $tags = [];
        $tags['search_str'] = App::$input['search_str'];

        if (strlen(App::$input['search_str'])>3) {
            $query="
            (SELECT id, 'article_item' as type, seo_alias, title, content, list_id, MATCH (title,content) AGAINST ('" . App::$input['search_str'] . "') AS score
            FROM article_item 
            WHERE MATCH (title,content) AGAINST ('" . App::$input['search_str'] . "'))
            UNION    
            (SELECT id, 'blog_post' as type, seo_alias, title, content, 0, MATCH (title,content) AGAINST ('" . App::$input['search_str'] . "') AS score
            FROM blog_posts
            WHERE MATCH (title,content) AGAINST ('" . App::$input['search_str'] . "'))
            order by score desc";
            $result=App::$db->query($query);
            if (!$result->num_rows) {
                App::addFlash('warning', 'Ничего не найдено.');
            }
        } else {
            App::addFlash('warning', 'Поисковый запрос слишком короткий.');
        }
        return App::$template->parse('search.html.twig', $tags, $result);
    }
}
