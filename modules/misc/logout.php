<?php 
if(!isset($input)) {
    require '../../include/common.php';
}

use Classes\App;
App::$user->delRememberme(App::$user->id,$COOKIE_NAME);
$_SESSION['UID']=null;
$_SESSION['FLAGS']='';
App::$user->logout();
redirect(App::$SUBDIR);
exit();
