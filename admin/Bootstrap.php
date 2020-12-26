<?php

namespace admin;

use classes\App;

class Bootstrap
{
    public function bootstrap() 
    {
        App::$template->addPath('admin/templates');
            
        App::$routing->addRoutes([
            'admin-index' => [
                'pattern' => '^admin\/?[\w\-]*$',
                'controller' => 'admin\controllers\IndexController'
            ],      
            'blog-edit' => [
                'pattern' => '^admin\/blog\-edit\/[\w\-]*$',
                'controller' => 'modules\blog\controllers\EditController',
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
            'users-edit' => [
                'pattern' => '^admin\/users\-edit\/[\w\-]*$',
                'controller' => 'modules\users\controllers\EditController',
            ],  
        ]);
    }
}
