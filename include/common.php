<?php

require_once 'global.php';

$DEBUG['start_time'] = microtime(true);

$_SESSION['UID']=0;
$_SESSION['UNAME']='';
$_SESSION['FLAGS']='';

session_cache_limiter('nocache');
session_name($SESSID);
session_start();

require_once $INC_DIR.'lib_sql.php';

if(is_array($_GET))foreach ($_GET as $key => $value) $input[$key]=db_test_param($value,$key);
if(is_array($_POST))foreach ($_POST as $key => $value) $input[$key]=db_test_param($value,$key);
if(is_array($_SERVER))foreach ($_SERVER as $key => $value) $server[$key]=$value;

require_once $INC_DIR.'lib_blocks.php';
require_once $INC_DIR.'lib_messages.php';
require_once $INC_DIR.'lib_templates.php';
require_once $INC_DIR.'lib_functions.php';

// Load settings into $settings[]
$query='SELECT * FROM settings';
$result=my_query($query,$conn,true);
while ($row = $result->fetch_array()) {
    $settings[$row['title']] = $row['value'];
}

function add_to_debug ($message) {
    global $settings, $DEBUG;
    if($settings['debug']){
        $time = microtime(true) - $DEBUG['start_time'];
        $time = sprintf('%.4F', $time);

        $DEBUG['log'][] = $time . "\t" . $message;
    }
}

add_to_debug('Settings loaded');

require_once $INC_DIR.'lib_stats.php';

add_to_debug('Stats added');

$query = "SELECT * FROM parts WHERE uri='" . $server["REQUEST_URI"] . "'";
$part = my_select_row($query, 1);
if (!$part[id]) {
    $query = "SELECT * FROM parts WHERE '" . $server["REQUEST_URI"] . "' LIKE concat('%',uri,'%') AND title<>'default'";
    $part = my_select_row($query, 1);
}
if (!$part[id]) {
    $query = "SELECT * FROM parts WHERE title='default'";
    $part = my_select_row($query, 1);
}
if (!$part[id]) {
    my_msg('default_tpl_not_found');
    exit();
}

add_to_debug('Part data loaded');

if ((strlen($part[user_flag])) && (!strstr($_SESSION['FLAGS'], $part['user_flag'])) && (!strstr($_SESSION['FLAGS'], 'global'))) {
    if ($_SESSION['UID']) {
        echo '<h1 align=center>У вас нет соответствующих прав !</h1>';
        exit();
    } else {
        $_SESSION['GO_TO_URI'] = $server['REQUEST_URI'];
        header('Location: ' . $SUBDIR . 'login/');
        exit;
    }
}

$JQUERY_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.js"></script>'."\n";
$JQUERY_FORM_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.form.js"></script>'."\n";

$EDITOR_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor.js"></script>'."\n";

$EDITOR_MINI_INC= ' <script type="text/javascript" src="'.$BASE_HREF.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor_mini.js"></script>'."\n";

$EDITOR_HTML_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/edit_area/edit_area_full.js" charset="utf-8">></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor_html.js" charset="utf-8"></script>'."\n";

$tags['nav_str']="<a href={$SUBDIR} class=nav_home>Главная</a>";

$server['PHP_SELF_DIR']=dirname($server['PHP_SELF']).'/';

$css_array=explode(';',$settings['css_list'].$tags['Add_CSS']);
foreach ($css_array as $css){
    $css='css/'.$css.'.css';
    if(file_exists($DIR.$css)){
        $tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.$css.'" type="text/css" rel=stylesheet />'."\n";
    } else {
        add_to_debug('CSS file missing: ' . $DIR.$css);
    }
}
unset($css_array,$css);
$tags['INCLUDE_HEAD']='';

