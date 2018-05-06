<?php

require_once 'include/common.php';
if($SUBDIR !== '/') {
    $request_uri = str_replace($SUBDIR, '', $_SERVER['REQUEST_URI']);
} else {
    $request_uri = substr($_SERVER['REQUEST_URI'], 1);
}    

$routes = [
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
    'vote' => [
        'pattern' => '^vote\/$',
        'file' => 'misc/vote.php'
    ],    
    
    'article_pdf' => [
        'pattern' => '^article\/(.*)\/(.*)\.pdf',
        'file' => 'article/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'pdf',
        ]
    ],    
    'article' => [
        'pattern' => '^article\/(.*)\/',
        'file' => 'article/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'blog' => [
        'pattern' => '^blog\/(.*)\/',
        'file' => 'blog/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'gallery' => [
        'pattern' => '^gallery\/(.*)\/',
        'file' => 'gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'gallery_index' => [
        'pattern' => '^gallery\/(.*)\/index.php',
        'file' => 'gallery/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'media' => [
        'pattern' => '^media\/(.*)\/',
        'file' => 'media/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    'news' => [
        'pattern' => '^news\/(.*)\/',
        'file' => 'news/index.php',
        'params' => [
            '1' => 'uri',
        ]
    ],    
    
    'catalog' => [
        'pattern' => '^test\/(.*)\/(.*)',
        'file' => 'catalog/index.php',
        'params' => [
            '1' => 'uri',
            '2' => 'item_title',
        ]
    ],    
];

foreach($routes as $title => $route) {
    if (preg_match('/'.$route['pattern'].'/', $request_uri, $matches) === 1) {
        add_to_debug("Match route '{$title}'");
        foreach($matches as $key => $value){
            if($key==0){
                $file=dirname(__FILE__) . DIRECTORY_SEPARATOR . $route['file'];
            } else {
                $input[$route['params'][$key]]=htmlspecialchars($value);
            }
        }
        break;
    }
}

/*
if($settings['debug']){
    echo $request_uri . '<br>';
    echo $file . '<br>';
    print_array($input);
}
 * 
 */

if(is_file($file)) {
    error_reporting(0);    
    $server['PHP_SELF'] = $SUBDIR.$route['file'];
    $server['PHP_SELF_DIR'] = $SUBDIR.dirname($route['file']) . '/';
    include_once $file;
    exit;
} 

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = $server['REQUEST_URI'];
$content = my_msg_to_str('file_not_found', $tags, '');

echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);



