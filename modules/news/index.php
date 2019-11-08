<?php

if (!isset($input)) {
    require '../../include/common.php';
}

$view_item = null;

if ((isset($input['uri'])) && (!isset($input['load']))) {
    $params = explode("/", $input['uri']);

    $query = "select id from news where seo_alias like '" . $params[0] . "'";
    $result = my_query($query);
    list($view_item) = $result->fetch_array();
}

$tags['Header'] = 'Новости';
$tags['INCLUDE_CSS'] .= '<link href="' . $SUBDIR . 'css/article_news_faq.css" type="text/css" rel=stylesheet />' . "\n";


$query = "select * from news " . ($view_item ? " where id='" . $view_item . "' " : '') . "order by date desc";
$result = my_query($query, true);

if ($view_item) {
    $row = $result->fetch_array();
    $tags['nav_str'] .= "<a href=\"" . $SUBDIR . "news/\" class=nav_next>{$tags['Header']}</a>";
    $tags['nav_str'] .= "<span class=nav_next>{$row['title']}</span>";    
    add_nav_item($tags['Header'], 'news/');
    add_nav_item($row['title']);
    
    // $tags['Header'] .= " - " . $row['title'];
    $tags['content'] = $row['content'];
    $result->data_seek(0);
} else {
    $tags['nav_str'] .= "<span class=nav_next>{$tags['Header']}</span>";
    add_nav_item($tags['Header']);
    $tags['content-cut'] = 'cut';
}

function get_news_full_content($tmp, $row) {
    global $tags;
    if($tags['content-cut']==='cut') {
        $tags['content'] = strip_tags($row['content']);
        $tags['content'] = cut_string($tags['content'], 250);
    } else {
        $tags['content'] = $row['content'];
    }
    return replace_base_href($tags['content']);
}


if (!$result->num_rows) {
    $content = my_msg_to_str('part_empty');
} else {
    $content = get_tpl_by_name('news_table', $tags, $result);
}
echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);

