<?php

$tags['Header'] = 'Генератор sitemap.xml';
require '../include/common.php';

use Classes\Sitemap;

$sitemap=new Sitemap();
// $sitemap->build_pages_array(array('article','blog','gallery'));
$types = explode(';', $settings['sitemap_types']);
$sitemap->build_pages_array($types);

$result=$sitemap->write();
$content=$result['output'];

$content.= my_msg_to_str('', [], 'Готово');

echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);

