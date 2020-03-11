<?php

namespace modules\article;

use Classes\BaseController;
use Classes\App;

class Controller extends BaseController
{    
    public function actionPartList()
    {
        $this->title = 'Статьи';
        $this->breadcrumbs[] = ['title'=>'Статьи'];
        $query = "select * from article_list";
        $result = App::$db->query($query);
        return App::$template->parse('article_list', [], $result);        
    }

    public function actionItemsList($alias)
    {
        $view_items = get_id_by_alias('article_list', $alias, true);
        $query = "select * from article_item where list_id='{$view_items}'";
        $result = App::$db->query($query);
        list($title) = App::$db->select_row("select title from article_list where id='{$view_items}'");
        $this->title = $title;
        $this->breadcrumbs[] = ['title'=>'Статьи','url'=>'article/'];
        $this->breadcrumbs[] = ['title'=>$title];
        return App::$template->parse('article_items', [], $result);
    }
    
    public function actionContent($part_alias,$alias)
    {
        $view_article = get_id_by_alias('article_item', $alias, true);
        $query = "select * from article_item where id='" . $view_article . "'";
        $result = App::$db->query($query);
        $row = $result->fetch_array();

        list($id, $title) = App::$db->select_row("select id,title from article_list where id='{$row['list_id']}'");

        $this->title = $row['title'];
        $this->breadcrumbs[] = ['title'=>'Статьи','url'=>'article/'];
        $this->breadcrumbs[] = ['title'=>$title,'url'=>get_article_list_href($id)];
        $this->breadcrumbs[] = ['title'=>$row['title']];

        $row['content'] = replace_base_href($row['content']);
        // $row['content'] = preg_replace('/width: \d+px;/', 'max-width: 100%;', $row['content']);
        $row['content'] = preg_replace('/style="width: /', 'class="img-fluid" style: style="width: ', $row['content']);
        $row['content'] = preg_replace('/height: \d+px/', 'height: auto; ', $row['content']);

        return  App::$template->parse('article_view', $row);
    }

    public function actionPDF($uri,$alias) {
        $id = get_id_by_alias('article_item', $alias, true);
        $query = "select * from article_item where id='" . $id . "'";
        $result = my_query($query, true);
        $row = $result->fetch_array();
        
        $PDF = new PDFView();
        return $PDF->get($row, $stream = true);
    }
    

}

