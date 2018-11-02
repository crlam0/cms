<?php 
if(!isset($input)) {
    require '../../include/common.php';
}
$_SESSION['UID']=null;
$_SESSION['FLAGS']='';
redirect($BASE_HREF);
exit();
