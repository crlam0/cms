<?php

return [
    'login' => [
        'pattern' => '^login\/$',
        'file' => 'misc/login.php'
    ],    
    'logout' => [
        'pattern' => '^logout\/$',
        'file' => 'misc/logout.php'
    ],    
    'passwd_change' => [
        'pattern' => '^passwd_change\/$',
        'file' => 'misc/passwd_change.php'
    ],    
    'search' => [
        'pattern' => '^search\/$',
        'file' => 'misc/search.php'
    ],    
    'request' => [
        'pattern' => '^request\/$',
        'file' => 'misc/request.php'
    ],    
    'misc_request' => [
        'pattern' => '^.*misc\/request\.php$',
        'file' => 'misc/request.php'
    ],    
    
    'article_pdf' => [
        'pattern' => '^article\/(.*)\/(.*)\.pdf$',
        'file' => 'article/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'pdf',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/(.*)\/$',
        'file' => 'article/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'blog' => [
        'pattern' => '^blog\/(.*)\/$',
        'file' => 'blog/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'gallery' => [
        'pattern' => '^gallery\/(.*)\/$',
        'file' => 'gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'gallery_index' => [
        'pattern' => '^gallery\/(.*)\/index\.php',
        'file' => 'gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'media' => [
        'pattern' => '^media\/(.*)\/$',
        'file' => 'media/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'news' => [
        'pattern' => '^news\/(.*)\/$',
        'file' => 'news/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    


    'catalog_index' => [
        'pattern' => '^catalog\/(.*)\/index\.php$',
        'file' => 'misc/search.php'
    ],    
    'catalog_buy' => [
        'pattern' => '^catalog\/(.*)\/buy\.php',
        'file' => 'catalog/buy.php'
    ], 
    'catalog_basket' => [
        'pattern' => '^catalog\/basket\/',
        'file' => 'catalog/buy.php'
    ], 
    'catalog' => [
        'pattern' => '^catalog\/(.*)\/$',
        'file' => 'catalog/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],
    'catalog_item' => [
        'pattern' => '^catalog\/(.*)\/(.*)$',
        'file' => 'catalog/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'item_title',
        ]
    ],    
];

