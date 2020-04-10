<?php


/* =========================================================================

  URL functions

  ========================================================================= */

use Classes\App;


/**
 * Redirect to $url
 *
 * @param string $url
 *
 */
function redirect($url) {
  $content = sprintf('<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="0;url=%1$s" />

    <title>Redirecting to %1$s</title>
  </head>
  <body>
    Redirecting to <a href="%1$s">%1$s</a>.
  </body>
  </html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

  header('Location: ' . $url);
  header('Status: 301 Moved Permanently', false, 301);

  die($content);
}


/* =========================================================================

  Menu functions

  ========================================================================= */

/**
 * Get HREF for article list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_article_list_href($list_id, $row = array()) {
    if (array_key_exists('id',$row)){
        $list_id = $row['id'];
    }
    if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
        return 'article/' . $row['seo_alias'] . '/';
    }    
    $query = "SELECT seo_alias FROM article_list WHERE id='{$list_id}'";
    $result = App::$db->query($query);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'article/' . $seo_alias . '/';
    } else {
        return 'article/?view_items=' . $list_id;
    }
}

/**
 * Get HREF for article
 *
 * @param integer $article_id Article ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_article_href($article_id, $row = array()) {
    if (array_key_exists('id',$row)){
        $article_id = $row['id'];
    }
    if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
        return get_article_list_href($row['list_id']) . $row['seo_alias'] . '/';
    }
    $query = "SELECT seo_alias,list_id FROM article_item WHERE id='{$article_id}'";
    $result = App::$db->query($query);
    list($seo_alias, $list_id) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return get_article_list_href($list_id) . $seo_alias . '/';
    } else {
        return 'article/?view=' . $article_id;
    }
}

/**
 * Get HREF for media list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_media_list_href($list_id, $row = array()) {
    if (array_key_exists('id',$row)){
        $list_id = $row['id'];
    }
    if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
        return 'media/' . $row['seo_alias'] . '/';
    }    
    $query = "SELECT seo_alias FROM media_list WHERE id='{$list_id}'";
    $result = App::$db->query($query);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'media/' . $seo_alias . "/";
    } else {
        return 'media/index.php?view_files=1&id=' . $list_id;
    }
}

/**
 * Get previous part array
 *
 * @param integer $prev_id Previous ID
 * @param integer $deep Deep
 * @param array $array Input Array
 *
 * @return array Output array
 */
function cat_prev_part($prev_id, $deep, $array) {
    $query = "SELECT id,title,prev_id,seo_alias FROM cat_part WHERE id='{$prev_id}' order by title asc";
    $result = App::$db->query($query);
    $array[$deep] = $result->fetch_array();
    if ($array[$deep]['prev_id']){
        $array = cat_prev_part($array[$deep]['prev_id'], $deep + 1, $array);
    }
    return $array;
}

/**
 * Get HREF for catalog part
 *
 * @param integer $part_id Part ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_cat_part_href($part_id, $row = array()) {
    if (array_key_exists('id',$row)){
        $part_id = $row['id'];
    }
    $uri = 'catalog/';
    if ($part_id) {
        $array = [];
        $array = cat_prev_part($part_id, 0, $array);
        $array = array_reverse($array);
        while (list ($n, $row) = @each($array)) {
            $uri.=$row['seo_alias'] . '/';
        }
    }
    return $uri;
}

/**
 * Get HREF for gallery list
 *
 * @param integer $list_id List ID
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_gallery_list_href($list_id, $row = array()) {
    if (array_key_exists('id',$row)){
        $list_id = $row['id'];
    }
    if (array_key_exists('seo_alias',$row) && strlen($row['seo_alias'])){
        return 'gallery/' . $row['seo_alias'] . '/';
    }    
    $query = "SELECT seo_alias FROM gallery_list WHERE id='{$list_id}'";
    $result = App::$db->query($query);
    list($seo_alias) = $result->fetch_array();
    if (strlen($seo_alias)) {
        return 'gallery/' . $seo_alias . '/';
    } else {
        return 'gallery/index.php?view_gallery=1&id=' . $list_id;
    }
}

/**
 * Get HREF for blog post
 *
 * @param integer $tmp Unused
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_post_href($tmp, $row) {
    if (check_key('seo_alias',$row)) {
        return "blog/" . $row['seo_alias'] . "/";
    } else {
        return "blog/" . "?view_post=" . check_key('id', $row);
    }
}

/**
 * Get HREF for menu item
 *
 * @param integer $tmp Unused
 * @param array $row Row from SQL query
 *
 * @return string Output string
 */
function get_menu_href($tmp, $row) {
    switch ($row['target_type']) {
        case "":
            return $row['href'];
        case "link":
            return $row['href'];
        case "article_list":
            return get_article_list_href($row['target_id']);
        case "article":
            return get_article_href($row['target_id']);
        case "media_list":
            return get_media_list_href($row['target_id']);
        case "catalog":
            return 'Все разделы каталога';
        case "cat_part":
            return get_cat_part_href($row['target_id']);
        case "gallery_list":
            return get_gallery_list_href($row['target_id']);
    }
}


/* =========================================================================

  Misc functions

  ========================================================================= */

/**
 * Replace BASE_HREF in content
 *
 * @param string $content Content string
 * @param boolean $direction Direction of replace
 *
 * @return string Output string
 */
function replace_base_href($content, $direction = false) {
    global $server, $SUBDIR;
    if ($direction) {
        return str_replace($server['REQUEST_SCHEME'] . '://' . $server["HTTP_HOST"] . $SUBDIR, "[%SUBDIR%]", $content);
    } else {
        return str_replace("[%SUBDIR%]", $server['REQUEST_SCHEME'] . '://' . $server["HTTP_HOST"] . $SUBDIR, $content);
    }
}

/**
 * Return path with subdir
 *
 * @param string $route Route template
 * @param array $params Params to replace in route
 *
 * @return string Output string
 */
function path($route,$params=[]){
    global $SUBDIR;
    if(count($params)){        
        foreach ($params as $item => $value) {
            $route = str_replace('{$'.$item.'}', $value, $route);
        }
    }
    return $SUBDIR.$route;
}


/**
 * Add item to breadcrumbs
 *
 * @param string $title Item title
 * @param string $url Item URL
 * @param boolean $skip_duplicates Return false if found duplicates
 *
 */
function add_nav_item($title, $url = null, $skip_duplicates = false) {
    global $tags;
    if($skip_duplicates) {
        foreach($tags['nav_array'] as $value) {
            if($value['title'] == $title ) {
                return false;
            }
        }
    }
    if($url) {
        $tags['nav_array'][] = [
            'title' => $title,
            'url' => $url
        ];
    } else {
        $tags['nav_array'][] = [
            'title' => $title,
        ];        
    }
}


/**
 * Get item ID by SEO alias
 *
 * @param string $table Table for search
 * @param string $seo_alias SEO alias
 * @param boolean $exit_with_404 Exit with 404 error if SEO alias not found
 *
 * @return integer ID of found item or null
 */
function get_id_by_alias ($table, $seo_alias, $exit_with_404 = false) {
    global $tags;
    $query="select id from {$table} where seo_alias = '{$seo_alias}'";
    $result=App::$db->query($query);
    list($id)=$result->fetch_array();
    if ((int)$id > 0) {
        return $id;
    } elseif ($exit_with_404) {
        header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        $tags['Header'] = 'Страница "' . $seo_alias . '" не найдена.';
        $content = App::$db->getRow('error', [], $tags['Header']);
        echo App::$template->parse(App::get('tpl_default'),$tags, null, $content);
        exit;
    } else {
        return null;
    }
}



