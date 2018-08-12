<?php

return [
    'login' => [
        'pattern' => '^login\/$',
        'file' => 'modules/misc/login.php'
    ],    
    'logout' => [
        'pattern' => '^logout\/$',
        'file' => 'modules/misc/logout.php'
    ],    
    'passwd_change' => [
        'pattern' => '^passwd_change\/$',
        'file' => 'modules/misc/passwd_change.php'
    ],    
    'search' => [
        'pattern' => '^search\/$',
        'file' => 'modules/misc/search.php'
    ],    
    'request' => [
        'pattern' => '^request\/$',
        'file' => 'modules/misc/request.php'
    ],    
    'request_php' => [
        'pattern' => '^.*misc\/request\.php$',
        'file' => 'modules/misc/request.php'
    ],    
    
    'article_pdf' => [
        'pattern' => '^article\/(.*)\/(.*)\.pdf$',
        'file' => 'modules/article/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'pdf',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/$',
        'file' => 'modules/article/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'article_uri' => [
        'pattern' => '^article\/(.*)\/$',
        'file' => 'modules/article/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'blog' => [
        'pattern' => '^blog\/$',
        'file' => 'modules/blog/index.php',
    ],    
    'blog_uri' => [
        'pattern' => '^blog\/(.*)\/$',
        'file' => 'modules/blog/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'faq' => [
        'pattern' => '^faq\/$',
        'file' => 'modules/faq/index.php',
    ],    
    'gallery' => [
        'pattern' => '^gallery\/$',
        'file' => 'modules/gallery/index.php',
    ],    
    'gallery_uri' => [
        'pattern' => '^gallery\/(.*)\/$',
        'file' => 'modules/gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'gallery_index' => [
        'pattern' => '^gallery\/(.*)\/index\.php',
        'file' => 'modules/gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'media' => [
        'pattern' => '^media\/$',
        'file' => 'modules/media/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'media_uri' => [
        'pattern' => '^media\/(.*)\/$',
        'file' => 'modules/media/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'news' => [
        'pattern' => '^news\/(.*)\/$',
        'file' => 'modules/news/index.php',
        'params' => [
            '1' => 'uri',
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
            '1' => 'uri',
        ]
    ],
    'catalog_item' => [
        'pattern' => '^catalog\/(.*)\/(.*)$',
        'file' => 'modules/catalog/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'item_title',
        ]
    ],    
];

