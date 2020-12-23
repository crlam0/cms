<?php

return [  
    'site-index' => [
        'pattern' => '^\/?$',
        'controller' => 'modules\misc\IndexController'
    ],    
    'site-index-php' => [
        'pattern' => '^index.php$',
        'controller' => 'modules\misc\IndexController',
        'action' => 'index'
    ],    
    'login' => [
        'pattern' => '^login\/?$',
        'controller' => 'modules\users\controllers\LoginController'
    ],    
    'logout' => [
        'pattern' => '^logout\/?$',
        'controller' => 'modules\users\controllers\LogoutController'
    ],    
    'passwd-change' => [
        'pattern' => '^passwd_change\/?$',
        'controller' => 'modules\users\controllers\PasswdChangeController'
    ],    
    'passwd-recovery' => [
        'pattern' => '^passwd_recovery\/?[\w\-]*$',
        'controller' => 'modules\users\controllers\PasswdRecoveryController'
    ],    
    'signup' => [
        'pattern' => '^signup\/?[\w\-]*$',
        'controller' => 'modules\users\controllers\SignupController'
    ],    
    'profile' => [
        'pattern' => '^profile\/?[\w\-]*$',
        'controller' => 'modules\users\controllers\ProfileController'
    ],    
    'search' => [
        'pattern' => '^search\/?$',
        'file' => 'modules/misc/search.php'
    ],    
    'request' => [
        'pattern' => '^request\/?$',
        'file' => 'modules/misc/request.php'
    ],    
    'request.php' => [
        'pattern' => '^.*misc\/request\.php$',
        'file' => 'modules/misc/request.php'
    ],   
    
    /*
     * Article related routes
     * 
     */

    'article-part-list' => [
        'pattern' => '^article\/?$',
        'controller' => 'modules\article\Controller',
        'action' => 'part-list',
    ],    
    'article-list' => [
        'pattern' => '^article\/([\w-]+)\/$',
        'controller' => 'modules\article\Controller',
        'action' => 'items-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/([\w-]+)\/([\w-]+)\/?$',
        'controller' => 'modules\article\Controller',
        'action' => 'content',
        'params' => [
            '0' => 'part_alias',
            '1' => 'alias'
        ]
    ],    
    'article-pdf' => [
        'pattern' => '^article\/(.*)\/(.*)\.pdf$',
        'controller' => 'modules\article\Controller',
        'action' => 'PDF',
        'params' => [
            '1' => 'uri',
            '2' => 'alias',
        ]
    ],    
    
    
    /*
     * Blog, FAQ related routes
     * 
     */    

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
    
    'faq-index' => [
        'pattern' => '^faq\/?[\w\-]*$',
        'controller' => 'modules\misc\FAQController',
    ],    
    'faq-index-with-page' => [
        'pattern' => '^faq\/page(\d+)\/?$',
        'controller' => 'modules\misc\FAQController',
        'action' => 'index',
        'params' => [
            '0' => 'page',
        ]
    ],

    /*
     * Gallery related routes
     * 
     */
    'gallery-part-list' => [
        'pattern' => '^gallery\/?$',
        'controller' => 'modules\gallery\Controller',
        'action' => 'part-list'
    ],    
    'gallery-load' => [
        'pattern' => '^gallery\/load$',
        'controller' => 'modules\gallery\Controller',
        'action' => 'load'
    ],    
    'gallery-images-list' => [
        'pattern' => '^gallery\/([\w-]+)\/?$',
        'controller' => 'modules\gallery\Controller',
        'action' => 'images-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'gallery-images-list-page' => [
        'pattern' => '^gallery\/([\w-]+)\/(\d+)\/?$',
        'controller' => 'modules\gallery\Controller',
        'action' => 'images-list',
        'params' => [
            '0' => 'alias',
            '1' => 'page',
        ]
    ],    
    
    /*
     * Media, news related routes
     * 
     */
    'media-download' => [
        'pattern' => '^media\/download$',
        'controller' => 'modules\media\Controller',
        'action' => 'download',
    ],    
    'media-part-list' => [
        'pattern' => '^media\/?$',
        'controller' => 'modules\media\Controller',
        'action' => 'part-list'
    ],    
    'media-files-list' => [
        'pattern' => '^media\/(\w+)\/?$',
        'controller' => 'modules\media\Controller',
        'action' => 'files-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'media-files-list-page' => [
        'pattern' => '^media\/(\w+)\/(\d+)\/?$',
        'controller' => 'modules\media\Controller',
        'action' => 'files-list',
        'params' => [
            '0' => 'alias',
            '1' => 'page',
        ]
    ],    

    'news-index' => [
        'pattern' => '^news\/?$',
        'controller' => 'modules\misc\NewsController',
    ],    
    'news-item-view' => [
        'pattern' => '^news\/([\w_\-]+)\/?$',
        'controller' => 'modules\misc\NewsController',
        'action' => 'item-view',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    
    'offers-index' => [
        'pattern' => '^offers\/?$',
        'controller' => 'modules\misc\OffersController',
    ],    
    'offers-item-view' => [
        'pattern' => '^offers\/([\w_\-]+)\/?$',
        'controller' => 'modules\misc\OffersController',
        'action' => 'item-view',
        'params' => [
            '0' => 'alias',
        ]
    ],    

    /*
     * Catalog, price related routes
     * 
     */

    'catalog' => [
        'pattern' => '^catalog\/$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'index',
    ],  
    'catalog-part' => [
        'pattern' => '^catalog\/(.*)\/$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'part-list',
        'params' => [
            '0' => 'uri',
        ]
    ],
    'catalog-item' => [
        'pattern' => '^catalog\/(.*)\/(.*)$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'item',
        'params' => [
            '0' => 'uri',
            '1' => 'item_title',
        ]
    ],
    'catalog-load-image' => [
        'pattern' => '^catalog\/load-image$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'load-image'
    ],
    
    'basket' => [
        'pattern' => '^basket\/[\w\-]*$',
        'controller' => 'modules\catalog\BasketController',
    ],
    
    'price' => [
        'pattern' => '^price\/[\w\-]*$',
        'controller' => 'modules\price\Controller',
    ],    
    
    
    
    
    /*
     * Controllers for admin/
     * 
     */
    
    'admin-index' => [
        'pattern' => '^admin\/?$',
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
    'templates-edit' => [
        'pattern' => '^admin\/templates\-edit\/[\w\-]*$',
        'controller' => 'admin\controllers\TemplatesEditController',
    ],    
    'slider-edit' => [
        'pattern' => '^admin\/slider\-edit\/[\w\-]*$',
        'controller' => 'admin\controllers\SliderEditController',
    ],
    'users-edit' => [
        'pattern' => '^admin\/users\-edit\/[\w\-]*$',
        'controller' => 'modules\users\controllers\EditController',
    ],
    
    
];

