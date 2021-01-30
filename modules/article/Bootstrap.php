<?php

namespace modules\article;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void 
    {
        App::$template->addPath('modules/article/views');
            
        App::$routing->addRoutes([
            'article-part-list' => [
                'pattern' => '^article\/?$',
                'controller' => 'modules\article\controllers\Controller',
                'action' => 'part-list',
            ],    
            'article-list' => [
                'pattern' => '^article\/([\w-]+)\/$',
                'controller' => 'modules\article\controllers\Controller',
                'action' => 'items-list',
                'params' => [
                    '0' => 'alias',
                ]
            ],    
            'article-list-with-page' => [
                'pattern' => '^article\/([\w-]+)\/page(\d+)\/$',
                'controller' => 'modules\article\controllers\Controller',
                'action' => 'items-list',
                'params' => [
                    '0' => 'alias',
                    '1' => 'page',
                ]
            ],    
            'article' => [
                'pattern' => '^article\/([\w-]+)\/([\w-]+)\/?$',
                'controller' => 'modules\article\controllers\Controller',
                'action' => 'content',
                'params' => [
                    '0' => 'list_alias',
                    '1' => 'alias'
                ]
            ],    
            'article-pdf' => [
                'pattern' => '^article\/(.*)\/(.*)\.pdf$',
                'controller' => 'modules\article\controllers\Controller',
                'action' => 'PDF',
                'params' => [
                    '1' => 'uri',
                    '2' => 'alias',
                ]
            ],
            
            /* For admin module */
            
            'article-list-edit' => [
                'pattern' => '^admin\/article\-edit\/[\w\-]*$',
                'controller' => 'modules\article\controllers\ListEditController',
            ],
            'article-item-edit' => [
                'pattern' => '^admin\/article\-edit\/items\/(\d+)\/[\w\-]*$',
                'controller' => 'modules\article\controllers\ItemEditController',
                'params' => [
                    '0' => 'list_id',
                ]
            ],
            
        ]);
        
        App::$routing->addGetUrlFunction('article_list', function ($list_id, $row) {
            if (!$list_id && array_key_exists('id',$row)){
                $list_id = $row['id'];
            }
            if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
                return 'article/' . $row['seo_alias'] . '/';
            }    
            list($seo_alias) = App::$db->getRow("SELECT seo_alias FROM article_list WHERE id=?", ['id' => $list_id]);
            return 'article/' . $seo_alias . '/';
        });
        
        App::$routing->addGetUrlFunction('article',  function ($article_id, $row) {
            if (!$article_id && array_key_exists('id', $row)){
                $article_id = $row['id'];
            }
            if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
                return App::$routing->getUrl('article_list', $row['list_id']) . $row['seo_alias'] . '/';
            }
            list($seo_alias, $list_id) = App::$db->getRow("SELECT seo_alias,list_id FROM article_item WHERE id=?", ['id' => $article_id]);
            return App::$routing->getUrl('article_list', $list_id) . $seo_alias . '/';
        });
        
    }
}
