<?php
include 'include/common.php';

use Classes\Routing;
use Classes\MyGlobal;

$Routing = new Routing ($server['REQUEST_URI']);
MyGlobal::set('Routing', $Routing );

if ($Routing->hasGETParams()) {
    $Routing->proceedGETParams();
}

if( $Routing->isIndexPage() and file_exists('index.local.php')) {
    $tags['isIndexPage'] = true;
    require 'index.local.php';
    exit;
}

$file = $Routing->getFileName();

if($file && is_file($DIR . $file)) {
    // error_reporting(0);    
    $server['PHP_SELF'] = $SUBDIR.$file;
    $server['PHP_SELF_DIR'] = $SUBDIR.dirname($file) . '/';

    require $DIR . $file;
    exit;
} 

$tags['Header'] = 'Ошибка 404';
$tags['file_name'] = $server['REQUEST_URI'];
$content = my_msg_to_str('file_not_found', $tags, '');
header($server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);


