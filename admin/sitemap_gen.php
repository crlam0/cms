<?php

$tags['Header'] = 'Генератор sitemap.xml';
require '../include/common.php';
require $INC_DIR . 'lib_sitemap.php';

$sitemap=new SITEMAP();
$sitemap->build_pages_array(array('article','blog','gallery'));
$result=$sitemap->write();
$content=$result['output'];

$content.= my_msg_to_str('', '', 'Готово');

echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
?>

