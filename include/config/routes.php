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
     * FAQ related routes
     * 
     */       
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
     * News related routes
     * 
     */  
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
   
       
];

