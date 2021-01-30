<?php

namespace modules\blog;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void 
    {
        App::$template->addPath('modules/blog/views');
            
        App::$routing->addRoutes([
            'blog-index' => [
                'pattern' => '^blog\/?$',
                'controller' => 'modules\blog\controllers\Controller',
                'action' => 'index',
            ],    
            'blog-index-with-page' => [
                'pattern' => '^blog\/page(\d+)\/?$',
                'controller' => 'modules\blog\controllers\Controller',
                'action' => 'index',
                'params' => [
                    '0' => 'page',
                ]
            ],    
            'add-score' => [
                'pattern' => '^blog\/add\-score$',
                'controller' => 'modules\blog\controllers\Controller',
                'action' => 'add-score',
                'params' => [
                    '0' => 'target_id',
                ]
            ],    
            'blog-post-view' => [
                'pattern' => '^blog\/([\w_\-]+)\/?$',
                'controller' => 'modules\blog\controllers\Controller',
                'action' => 'post-view',
                'params' => [
                    '0' => 'alias',
                ]
            ],    
            'blog-by-tag' => [
                'pattern' => '^blog\/by\-tag\/([\w_\-]+)\/?$',
                'controller' => 'modules\blog\controllers\Controller',
                'action' => 'by-tag',
                'params' => [
                    '0' => 'alias',
                ]
            ],

            /* For admin module */
            
            'blog-edit' => [
                'pattern' => '^admin\/blog\-edit\/[\w\-]*$',
                'controller' => 'modules\blog\controllers\EditController',
            ],
        ]);
        
        App::$routing->addGetUrlFunction('blog_post',  function ($id, $row) {
            if(isset($row['seo_alias'])) {
                return 'blog/' . $row['seo_alias'] . '/';
            } else {
                return 'blog/' . $id . '/';                    
            }
        });
    }
}
