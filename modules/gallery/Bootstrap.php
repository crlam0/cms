<?php

namespace modules\gallery;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void 
    {
        App::$template->addPath('modules/gallery/views');
            
        App::$routing->addRoutes([
            'gallery-part-list' => [
                'pattern' => '^gallery\/?$',
                'controller' => 'modules\gallery\controllers\Controller',
                'action' => 'part-list'
            ],    
            'gallery-load' => [
                'pattern' => '^gallery\/load$',
                'controller' => 'modules\gallery\controllers\Controller',
                'action' => 'load'
            ],    
            'gallery-images-list' => [
                'pattern' => '^gallery\/([\w-]+)\/?$',
                'controller' => 'modules\gallery\controllers\Controller',
                'action' => 'images-list',
                'params' => [
                    '0' => 'alias',
                ]
            ],    
            'gallery-images-list-page' => [
                'pattern' => '^gallery\/([\w-]+)\/(\d+)\/?$',
                'controller' => 'modules\gallery\controllers\Controller',
                'action' => 'images-list',
                'params' => [
                    '0' => 'alias',
                    '1' => 'page',
                ]
            ],     
            /* For admin module */
            
            'gallery-list-edit' => [
                'pattern' => '^admin\/gallery\-edit\/[\w\-]*$',
                'controller' => 'modules\gallery\controllers\ListEditController',
            ],
            'gallery-image-edit' => [
                'pattern' => '^admin\/gallery\-edit\/items\/(\d+)\/[\w\-]*$',
                'controller' => 'modules\gallery\controllers\ImagesEditController',
                'params' => [
                    '0' => 'gallery_id',
                ]
            ],
        ]);
        
        App::$routing->addGetUrlFunction('gallery_list', function ($list_id, $row) {
            if (!$list_id && array_key_exists('id',$row)){
                $list_id = $row['id'];
            }
            if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
                return 'gallery/' . $row['seo_alias'] . '/';
            }    
            list($seo_alias) = App::$db->getRow("SELECT seo_alias FROM gallery_list WHERE id=?", ['id' => $list_id]);
            return 'gallery/' . $seo_alias . '/';
        }); 
    }
}
