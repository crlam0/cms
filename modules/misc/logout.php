<?php 
if(!isset($input)) {
    require '../../include/common.php';
}
user_del_rememberme();
$_SESSION['UID']=null;
$_SESSION['FLAGS']='';
redirect($BASE_HREF);
exit();
