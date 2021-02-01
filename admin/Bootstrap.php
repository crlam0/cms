<?php

namespace admin;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void
    {
        App::$template->addPath('admin/views');

        App::$routing->addRoutes([
            'admin-index' => [
                'pattern' => '^admin\/?[\w\-]*$',
                'controller' => 'admin\controllers\IndexController'
            ],
            'faq-edit' => [
                'pattern' => '^admin\/faq\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\FaqEditController',
            ],
            'feedback-edit' => [
                'pattern' => '^admin\/feedback\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\FeedbackEditController',
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
            'messages-edit' => [
                'pattern' => '^admin\/messages\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\MessagesEditController',
            ],
            'news-edit' => [
                'pattern' => '^admin\/news\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\NewsEditController',
            ],
            'offers-edit' => [
                'pattern' => '^admin\/offers\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\OffersEditController',
            ],
            'partners-edit' => [
                'pattern' => '^admin\/partners\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\PartnersEditController',
            ],
            'parts-edit' => [
                'pattern' => '^admin\/parts\-edit\/[\w\-]*$',
                'controller' => 'admin\controllers\PartsEditController',
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
