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

    'article-part-list' => [
        'pattern' => '^article\/?$',
        'controller' => 'modules\article\Controller',
        'action' => 'article-part-list',
    ],    
    'article-list' => [
        'pattern' => '^article\/(\w+)\/$',
        'controller' => 'modules\article\Controller',
        'action' => 'article-list',
        'params' => [
            '0' => 'alias',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/(\w+)\/(\w+)\/?$',
        'controller' => 'modules\article\Controller',
        'action' => 'article',
        'params' => [
            '0' => 'part_alias',
            '1' => 'alias'
        ]
    ],    
    
    
    
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
    
    'gallery' => [
        'pattern' => '^gallery\/?$',
        'file' => 'modules/gallery/index.php',
    ],    
    'gallery_uri' => [
        'pattern' => '^gallery\/(.*)\/?$',
        'file' => 'modules/gallery/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    'gallery_index' => [
        'pattern' => '^gallery\/(.*)\/index\.php',
        'file' => 'modules/gallery/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],    
    
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


    'catalog' => [
        'pattern' => '^catalog\/$',
        'file' => 'modules/catalog/index.php'
    ],    
    'catalog_index' => [
        'pattern' => '^catalog\/(.*)\/index\.php$',
        'file' => 'modules/catalog/index.php'
    ],    
    'catalog_buy' => [
        'pattern' => '^catalog\/(.*)\/buy\.php',
        'file' => 'modules/catalog/buy.php'
    ], 
    'catalog_basket' => [
        'pattern' => '^catalog\/basket\/',
        'file' => 'modules/catalog/buy.php'
    ], 
    'catalog_part' => [
        'pattern' => '^catalog\/(.*)\/$',
        'file' => 'modules/catalog/index.php',
        'params' => [
            '0' => 'uri',
        ]
    ],
    'catalog_item' => [
        'pattern' => '^catalog\/(.*)\/(.*)$',
        'file' => 'modules/catalog/index.php',
        'params' => [
            '0' => 'uri',
            '1' => 'item_title',
        ]
    ],    
    'price' => [
        'pattern' => '^price\/$',
        'file' => 'modules/price/index.php'
    ],    
];

