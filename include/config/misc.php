<?php

$JQUERY_INC='';
$JQUERY_FORM_INC='<script type="text/javascript" src="'.$SUBDIR.'include/js/jquery.form.js"></script>'."\n";

$EDITOR_INC='<script type="text/javascript" src="'.$SUBDIR.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$SUBDIR.'include/js/editor.js"></script>'."\n";

$EDITOR_MINI_INC= ' <script type="text/javascript" src="'.$SUBDIR.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$SUBDIR.'include/js/editor_mini.js"></script>'."\n";

$EDITOR_HTML_INC='<script type="text/javascript" src="'.$SUBDIR.'include/edit_area/edit_area_full.js" charset="utf-8">></script>'."\n".
'<script type="text/javascript" src="'.$SUBDIR.'include/js/editor_html.js" charset="utf-8"></script>'."\n";

$tags['nav_str']='<a href='.$SUBDIR.' class="nav_home">Главная</a>';
$tags['nav_array'][] = [
    'url' => '',
    'title' => 'Главная'
];

$console = [
    'cachePaths' => [
        'twig' => 'var/cache/twig',
        'catalog' => 'var/cache/catalog',
        'gallery' => 'var/cache/gallery',
    ],
];
    
