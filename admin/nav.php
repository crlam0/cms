<!-- ================== MENU ================== -->
<?php

use classes\App;

if(App::$user->haveFlag('admin')) {
    if(file_exists(__DIR__.'/../local/admin-nav.php')) {
        require __DIR__.'/../local/admin-nav.php';
    } else {
        die('File local/admin-nav.php not found !');
    }
}
