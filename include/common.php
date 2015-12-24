<?php

include_once "global.php";

$session["UID"]=0;
$session["UNAME"]='';
$session["FLAGS"]='';

session_cache_limiter('nocache');
session_name($SESSID);
session_start();

include_once $INC_DIR."lib_sql.php";

if(is_array($_GET))foreach ($_GET as $key => $value) $input[$key]=db_test_param($value,$key);
if(is_array($_POST))foreach ($_POST as $key => $value) $input[$key]=db_test_param($value,$key);
if(is_array($_SESSION))foreach ($_SESSION as $key => $value) $session[$key]=$value;
if(is_array($_SERVER))foreach ($_SERVER as $key => $value) $server[$key]=$value;

include_once $INC_DIR."lib_blocks.php";
include_once $INC_DIR."lib_messages.php";
include_once $INC_DIR."lib_templates.php";
include_once $INC_DIR."lib_functions.php";

// Load settings into $settings[]
$query="select * from settings";
$result=my_query($query,$conn,1);
while ($row = $result->fetch_array()) {
    $settings[$row['title']] = $row['value'];
}

include_once $INC_DIR."lib_stats.php";

$query = "select * from parts where uri='" . $server["REQUEST_URI"] . "'";
$part = my_select_row($query, 1);
if (!$part[id]) {
    $query = "select * from parts where '" . $server["REQUEST_URI"] . "' like concat('%',uri,'%') and title<>'default'";
    $part = my_select_row($query, 1);
}
if (!$part[id]) {
    $query = "select * from parts where title='default'";
    $part = my_select_row($query, 1);
}
if (!$part[id]) {
    my_msg('default_tpl_not_found');
    exit();
}

if ((strlen($part[user_flag])) && (!strstr($session["FLAGS"], $part[user_flag])) && (!strstr($session["FLAGS"], "global"))) {
    if ($session['UID']) {
        echo "<h1 align=center>У вас нет соответствующих прав !</h1>";
        exit();
    } else {
        $session['GO_TO_URI'] = $server['REQUEST_URI'];
        header('Location: ' . $SUBDIR . 'login/');
        exit;
    }
}


$JQUERY_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.js"></script>'."\n";
$JQUERY_FORM_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/js/jquery.form.js"></script>'."\n";

$EDITOR_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor.js"></script>'."\n";

$EDITOR_MINI_INC= ' <script type="text/javascript" src="'.$BASE_HREF.'include/ckeditor/ckeditor.js" charset="utf-8"></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor_mini.js"></script>'."\n";;

$EDITOR_HTML_INC='<script type="text/javascript" src="'.$BASE_HREF.'include/edit_area/edit_area_full.js" charset="utf-8">></script>'."\n".
'<script type="text/javascript" src="'.$BASE_HREF.'include/js/editor_html.js" charset="utf-8"></script>'."\n";;

$tags[nav_str]="<a href=$SUBDIR class=nav_home>Главная</a>";

$server["PHP_SELF_DIR"]=dirname($server["PHP_SELF"])."/";

$tags[head_inc]="";

?>