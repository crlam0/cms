<?php
if(!isset($input)) {
    require '../../include/common.php';
}
add_nav_item('Статьи', 'article/');

$content = '';

if (isset($input['uri'])) {
    $params = explode('/', $input['uri']);
    if(isset($params[1]) && strlen($params[1])){
        $input['view']=$params[1];
    }else{
        $view_items = get_id_by_alias('article_list', $params[0], true);
    }
}

if (isset($input['view'])) {
    $view_article = get_id_by_alias('article_item', $input['view'], true);
}

if (isset($input['view_items'])) {
    $view_items = $input['id'];
}

if (!$input->count()) {
    $view_items = null;
}

if ($view_article) {
    $query = "select * from article_item where id='" . $view_article . "'";
    $result = my_query($query);
    $row = $result->fetch_array();

    list($id, $title) = my_select_row("select id,title from article_list where id='{$row['list_id']}'", 1);
    $tags['Header'] = $row['title'];

    add_nav_item($title, get_article_list_href($id));
    add_nav_item($row['title']);
    
    $row['content'] = replace_base_href($row['content']);
    // $row['content'] = preg_replace('/width: \d+px;/', 'max-width: 100%;', $row['content']);
    $row['content'] = preg_replace('/style="width: /', 'class="img-fluid" style: style="width: ', $row['content']);
    
    $content = get_tpl_by_name('article_view', $row, $result);
    echo get_tpl_default($tags, null, $content);
    exit;
}


if ($view_items) {
    $query = "select * from article_item where list_id='{$view_items}'";
    $result = my_query($query, true);
    
    list($title) = my_select_row("select title from article_list where id='{$view_items}'", 1);
    $tags['Header'] = $title;

    add_nav_item($title);

    $content = get_tpl_by_name('article_items', $row, $result);
    echo get_tpl_default($tags, null, $content);
    exit;
}

$tags['Header'] = 'Статьи';

$query = "select * from article_list";
$result = my_query($query, true);

$content .= get_tpl_by_name('article_list', $row, $result);
echo get_tpl_default($tags, null, $content);
