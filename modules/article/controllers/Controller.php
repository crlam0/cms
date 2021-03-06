<?php

namespace modules\article\controllers;

use classes\App;
use classes\BaseController;
use classes\Pagination;
use modules\article\PDFView;

class Controller extends BaseController
{

    public function actionPartList(): string
    {
        $this->title = 'Статьи';
        $this->breadcrumbs[] = ['title' => 'Статьи'];
        $query = "select * from article_list order by title asc";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            return $this->render('article_list', [], $result);
        } else {
            return App::$message->get('list_empty');
        }
    }

    public function actionItemsList(string $alias, int $page = 1): string
    {
        $list_id = get_id_by_alias('article_list', $alias, true);

        [$title, $list_seo_alias] = App::$db->getRow("select title,seo_alias from article_list where id=?", ['id' => $list_id]);

        $this->title = $title;
        $this->breadcrumbs[] = ['title' => 'Статьи','url' => 'article/'];
        $this->breadcrumbs[] = ['title' => $title];

        $query = "SELECT count(id) from article_item where list_id=?";
        [$total] = App::$db->getRow($query, ['list_id' => $list_id]);

        $per_page = App::$settings['modules']['article']['article_per_page'] ?? 10;

        $pager = new Pagination($total, $page, $per_page);
        $tags['pager'] = $pager;
        $tags['article_list_href'] = 'article/' . $list_seo_alias . '/';

        $query = "select * from article_item where active='Y' and list_id=? order by title asc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query, ['list_id' => $list_id]);

        if ($result->num_rows) {
            return $this->render('article_items', [], $result);
        } else {
            return App::$message->get('list_empty');
        }
    }

    public function actionContent(string $list_alias, string $alias): string
    {
        $article_id = get_id_by_alias('article_item', $alias, true);
        $row = App::$db->getById('article_item', $article_id);

        [$id, $title] = App::$db->getRow("select id,title from article_list where id=?", ['list_id' => $row['list_id']]);

        $this->title = $row['title'];
        if ($row['active'] == 'Y') {
            $this->breadcrumbs[] = ['title'=>'Статьи', 'url'=>'article/'];
            $this->breadcrumbs[] = ['title'=>$title, 'url'=>App::$routing->getUrl('article_list', $id)];
        }
        $this->breadcrumbs[] = ['title'=>$row['title']];

        $row['content'] = replace_base_href($row['content']);
        // $row['content'] = preg_replace('/width: \d+px;/', 'max-width: 100%;', $row['content']);
        $row['content'] = preg_replace('/style="width: /', 'class="img-fluid" style="width: ', $row['content']);
        $row['content'] = preg_replace('/height: \d+px/', 'height: auto; ', $row['content']);

        return  App::$template->parse('article_view', $row);
    }

    public function actionPDF(string $alias): string
    {
        $id = get_id_by_alias('article_item', $alias, true);
        $data = App::$db->getById('article_item', $id);
        $PDF = new PDFView();
        return $PDF->get($data, 'article_view', $stream = true);
    }
}
