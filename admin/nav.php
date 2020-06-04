<!-- ================== MENU ================== -->
<?php

use classes\App;

if(!App::$user->haveFlag('admin')) {
    $content = App::$message->get('error', [], 'У вас нет соответствующих прав !');
    echo App::$template->parse(App::get('tpl_default'), [], null, $content);
    exit;
}

if(file_exists(__DIR__.'/../local/admin-nav.php')) {
    require __DIR__.'/../local/admin-nav.php';
} else {
    die('File local/admin-nav.php not found !');
}

