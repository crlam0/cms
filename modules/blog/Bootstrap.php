<?php

namespace modules\blog;

use classes\App;

class Bootstrap
{
    public function bootstrap() 
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
        ]);
    }
}
