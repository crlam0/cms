<?php
if(!isset($input)) {
    require '../../include/common.php';
}

$tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.'css/article_news_faq.css" type="text/css" rel=stylesheet />'."\n";


if(file_exists($INC_DIR . 'dompdf/src/Autoloader.php')) {
    require_once $INC_DIR . 'dompdf/lib/html5lib/Parser.php';
    require_once $INC_DIR . 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
    require_once $INC_DIR . 'dompdf/lib/php-svg-lib/src/autoload.php';
    require_once $INC_DIR . 'dompdf/src/Autoloader.php';
    Dompdf\Autoloader::register();
    $dompdf_enabled = true;
}
use Dompdf\Dompdf;

if (isset($input['pdf']) && $dompdf_enabled) {
    $params = explode('/', $input['uri']);
    if(strlen($params[1])){
        $input['view']=$params[1];
        $query = "select * from article_item where seo_alias like '" . $input['view'] . "'";
        $result = my_query($query, true);
        if(!$result->num_rows) {
            header('Location: ' . $SUBDIR . '');
            exit ();
        }
        $row = $result->fetch_array();
        $row['content'] = replace_base_href($row['content']);
        
        $content = "<html><head><style>body { font-family: times; }</style>".
        "<body>"; 
        
        $content .= '<h1>' . $row['title'] . '</h1><br />';
        $content .= get_tpl_by_title('article_view', $row, $result);  
        
        $content .="</body>".
        "</head></html>";

        $dompdf = new Dompdf();
        $dompdf->loadHtml($content);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        // $dompdf->stream(encodestring($row['title']) . '.pdf');
        // $dompdf->stream($row['title'] . '.pdf');
        // header('Content-Description: File Transfer');
        
        header('Content-Type: content/pdf');
        header('Content-Disposition: attachment; filename=' . $row['title'] . '.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        ob_clean();
        flush();
        echo $dompdf->output();
    }
    exit();        
}

if (isset($input['uri'])) {
    $params = explode('/', $input['uri']);
    if(isset($params[1]) && strlen($params[1])){
        $input["view"]=$params[1];
    }else{
        $query="select id from article_list where seo_alias like '".$params[0]."'";
        $result=my_query($query);
        list($view_items)=$result->fetch_array();
    }
}

if (isset($input['view'])) {
    $query="select id from article_item where seo_alias like '".$input['view']."'";
    $result=my_query($query);
    list($article_id)=$result->fetch_array();
    $input['id'] = ( is_numeric($article_id) ? $article_id : $input['view']);
    $input['view_article'] = 1;
}

if (isset($input['view_items'])) {
    $view_items = $input['id'];
}


if (!$input->count()) {
    $view_items = '';
    $tags['Header'] = 'Статьи';
}

if ($input["view_article"]) {
    $query = "select * from article_item where id='" . $input['id'] . "'";
    $result = my_query($query);
    $row = $result->fetch_array();

    $tags['nav_str'].="<a href=" . $SUBDIR . "article/ class=nav_next>Статьи</a>";
    list($id, $title) = my_select_row("select id,title from article_list where id='{$row['list_id']}'", 1);
    $tags['nav_str'].="<span class=nav_next><a href=\"".$SUBDIR.get_article_list_href($id)."\">$title</a></span>";
    $tags['nav_str'].="<span class=nav_next>{$row['title']}</span>";
    $tags['Header'] = $row['title'];

    add_nav_item('Статьи', 'article/');
    add_nav_item($title, get_article_list_href($id));
    add_nav_item($row['title']);
    
    $row['content'] = replace_base_href($row['content']);
    
    $content = get_tpl_by_title('article_view', $row, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}


if ($view_items) {
    $query = "select * from article_item where list_id='{$view_items}'";
    $result = my_query($query, true);
    
    $tags['nav_str'].="<a href=" . $server["PHP_SELF_DIR"] . " class=nav_next>Статьи</a>";
    list($title) = my_select_row("select title from article_list where id='{$view_items}'", 1);
    $tags['nav_str'].="<span class=nav_next>$title</span>";
    $tags['Header'] = $title;

    add_nav_item('Статьи', 'article/');
    add_nav_item($title);

    $content = get_tpl_by_title('article_items', $row, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}


if (!isset($_SESSION["view_items"])) {
    $query = "select * from article_list";
    $result = my_query($query, true);

    $tags['nav_str'].="<span class=nav_next>Статьи</span>";
    add_nav_item('Статьи');

    $content = get_tpl_by_title('article_list', $row, $result);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}