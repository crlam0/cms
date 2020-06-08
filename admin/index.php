<?php
$tags['Header']='Страница администрирования';
require '../include/common.php';

$controller = new admin\Controllers\IndexController();
$content = $controller->actionIndex();

echo get_tpl_default($tags, [], $content);


