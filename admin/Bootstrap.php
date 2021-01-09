<?php

namespace admin;

use classes\App;

class Bootstrap
{
    public function bootstrap() 
    {
        App::$template->addPath('admin/views');
            
        App::$routing->addRoutes([
            'admin-index' => [
                'pattern' => '^admin\/?[\w\-]*$',
                'controller' => 'admin\controllers\IndexController'
            ],      
            'menu-list-edit' => [
                'pattern' => '^admin\/menu\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\MenuListEditController',
            ],
            'menu-item-edit' => [
                'pattern' => '^admin\/menu\-edit\/items\/(\d+)\/[\w\-]*$',
                'controller' => 'admin\controllers\MenuItemEditController',
                'params' => [
                    '0' => 'menu_id',
                ]
            ],
            'news-edit' => [
                'pattern' => '^admin\/news\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\NewsEditController',
            ],
            'offers-edit' => [
                'pattern' => '^admin\/offers\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\OffersEditController',
            ],
            'settings-edit' => [
                'pattern' => '^admin\/settings\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\SettingsEditController',
            ],
            'slider-edit' => [
                'pattern' => '^admin\/slider\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\SliderEditController',
            ],
            'templates-edit' => [
                'pattern' => '^admin\/templates\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\TemplatesEditController',
            ],    
        ]);
    }
}
