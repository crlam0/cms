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
        'pattern' => '^passwd_recovery\/?\w*$',
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
        'pattern' => '^article\/(\w+)\/$',
        'controller' => 'modules\article\Controller',
        'action' => 'items-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/(\w+)\/(\w+)\/?$',
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
    
    'blog' => [
        'pattern' => '^blog\/?$',
        'file' => 'modules/blog/index.php',
    ],    
    'blog_uri_with_slash' => [
        'pattern' => '^blog\/(.*)\/$',
        'file' => 'modules/blog/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    'blog_uri' => [
        'pattern' => '^blog\/(.*)$',
        'file' => 'modules/blog/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    
    'faq_uri' => [
        'pattern' => '^faq\/(.*)\/$',
        'file' => 'modules/faq/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    'faq' => [
        'pattern' => '^faq\/.*$',
        'file' => 'modules/faq/index.php',
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
        'pattern' => '^gallery\/(\w+)\/?$',
        'controller' => 'modules\gallery\Controller',
        'action' => 'images-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'gallery-images-list-page' => [
        'pattern' => '^gallery\/(\w+)\/(\d+)\/?$',
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
    
    'media' => [
        'pattern' => '^media\/?$',
        'file' => 'modules/media/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    'media_uri' => [
        'pattern' => '^media\/(.*)\/?$',
        'file' => 'modules/media/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
     */   
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
    
    'news' => [
        'pattern' => '^news\/?$',
        'file' => 'modules/news/index.php',
    ],    
    'news_uri' => [
        'pattern' => '^news\/(.*)\/?$',
        'file' => 'modules/news/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    'offers' => [
        'pattern' => '^offers\/?$',
        'file' => 'modules/offers/index.php',
    ],    
    'offers_uri' => [
        'pattern' => '^offers\/(.*)\/?$',
        'file' => 'modules/offers/index.php',
        'params' => [
            '0' => 'uri',
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
    'catalog-add-buy' => [
        'pattern' => '^catalog\/add-buy$',
        'controller' => 'modules\catalog\Controller',
        'action' => 'add-buy'
    ],
    
    'basket' => [
        'pattern' => '^basket\/.*$',
        'controller' => 'modules\catalog\BasketController',
    ],
    
    'catalog_buy' => [
        'pattern' => '^catalog\/(.*)\/buy\.php',
        'file' => 'modules/catalog/buy.php'
    ], 
    'catalog_basket' => [
        'pattern' => '^catalog\/basket\/',
        'file' => 'modules/catalog/buy.php'
    ], 
    'price' => [
        'pattern' => '^price\/$',
        'file' => 'modules/price/index.php'
    ],    
];

