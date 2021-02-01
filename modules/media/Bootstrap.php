<?php

namespace modules\media;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void 
    {
        App::$template->addPath('modules/media/views');
            
        App::$routing->addRoutes([
            'media-download' => [
                'pattern' => '^media\/download$',
                'controller' => 'modules\media\controllers\Controller',
                'action' => 'download',
            ],    
            'media-part-list' => [
                'pattern' => '^media\/?$',
                'controller' => 'modules\media\controllers\Controller',
                'action' => 'part-list'
            ],    
            'media-files-list' => [
                'pattern' => '^media\/(\w+)\/?$',
                'controller' => 'modules\media\controllers\Controller',
                'action' => 'files-list',
                'params' => [
                    '0' => 'alias',
                ]
            ],    
            'media-files-list-page' => [
                'pattern' => '^media\/(\w+)\/(\d+)\/?$',
                'controller' => 'modules\media\controllers\Controller',
                'action' => 'files-list',
                'params' => [
                    '0' => 'alias',
                    '1' => 'page',
                ]
            ],     
            /* For admin module */
            
            'media-list-edit' => [
                'pattern' => '^admin\/media\-edit\/[\w\-]*$',
                'controller' => 'modules\media\controllers\ListEditController',
            ],
            'media-file-edit' => [
                'pattern' => '^admin\/media\-edit\/items\/(\d+)\/[\w\-]*$',
                'controller' => 'modules\media\controllers\FileEditController',
                'params' => [
                    '0' => 'list_id',
                ]
            ],
        ]);
        
        App::$routing->addGetUrlFunction('media_list', function ($list_id, $row) {
            if (!$list_id && array_key_exists('id',$row)){
                $list_id = $row['id'];
            }
            if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
                return 'media/' . $row['seo_alias'] . '/';
            }    
            list($seo_alias) = App::$db->getRow("SELECT seo_alias FROM media_list WHERE id=?", ['id' => $list_id]);
            return 'media/' . $seo_alias . "/";
        });
        
    }
}
