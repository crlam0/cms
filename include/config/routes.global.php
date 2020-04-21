<?php

return [  
    'login' => [
        'pattern' => '^login\/?$',
        'controller' => 'modules\misc\LoginController'
    ],    
    'logout' => [
        'pattern' => '^logout\/?$',
        'controller' => 'modules\misc\LogoutController'
    ],    
    'passwd_change' => [
        'pattern' => '^passwd_change\/?$',
        'controller' => 'modules\misc\PasswdChangeController'
    ],    
    'passwd_recovery' => [
        'pattern' => '^passwd_recovery\/?[\w\-]*$',
        'controller' => 'modules\misc\PasswdRecoveryController'
    ],    
    'search' => [
        'pattern' => '^search\/?$',
        'file' => 'modules/misc/search.php'
    ],    
    'request' => [
        'pattern' => '^request\/?$',
        'file' => 'modules/misc/request.php'
    ],    
    'request_php' => [
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
    'article_pdf' => [
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
        'controller' => 'modules\misc\BlogController',
        'action' => 'index',
    ],    
    'blog-index-with-page' => [
        'pattern' => '^blog\/page(\d+)\/?$',
        'controller' => 'modules\misc\BlogController',
        'action' => 'index',
        'params' => [
            '0' => 'page',
        ]
    ],    
    'blog-post-view' => [
        'pattern' => '^blog\/([\w_\-]+)\/?$',
        'controller' => 'modules\misc\BlogController',
        'action' => 'post-view',
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
    'catalog_part' => [
        'pattern' => '^catalog\/(.*)\/$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'part-list',
        'params' => [
            '0' => 'uri',
        ]
    ],
    'catalog_item' => [
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
];

