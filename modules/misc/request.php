<?php

if(!isset($input)) {
    require '../../include/common.php';
}


if(file_exists('request.local.php')) {
    require('request.local.php');
}
