<?php 
if(!isset($input)) {
    require '../../include/common.php';
}

use Classes\App;
App::$user->delRememberme(App::$user->id,$COOKIE_NAME);
App::$user->logout();
$_SESSION['UID']=0;
$_SESSION['FLAGS']='';
redirect(App::$SUBDIR);
exit();
