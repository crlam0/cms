<?php


/* =========================================================================

  URL functions

  ========================================================================= */

use classes\App;


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
    if ($direction) {
        return str_replace(App::$server['REQUEST_SCHEME'] . '://' . App::$server["HTTP_HOST"] . App::$SUBDIR, "[%SUBDIR%]", $content);
    } else {
        // return str_replace("[%SUBDIR%]", $server['REQUEST_SCHEME'] . '://' . $server["HTTP_HOST"] . $SUBDIR, $content);
        return str_replace("[%SUBDIR%]", App::$SUBDIR, $content);
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
function path($route, $params=[]){
    if(count($params)){        
        foreach ($params as $item => $value) {
            $route = str_replace('{$'.$item.'}', $value, $route);
        }
    }
    return App::$SUBDIR.$route;
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
    list($id) = App::$db->getRow("select id from {$table} where seo_alias = ?", ['seo_alias' => $seo_alias]);
    if ((int)$id > 0) {
        return $id;
    } elseif ($exit_with_404) {
        header(App::$server['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        $tags['Header'] = 'Страница "' . $seo_alias . '" не найдена.';
        $content = App::$message->get('error', [], $tags['Header']);
        echo App::$template->parse(App::get('tpl_default'),$tags, null, $content);
        exit;
    } else {
        return null;
    }
}



