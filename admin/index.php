<?php
$tags['Header']='Страница администрирования';
require '../include/common.php';

$controller = new admin\controllers\IndexController();
$content = $controller->actionIndex();

echo  App::$template->parse($part['tpl_name'], $tags, null, $content);
