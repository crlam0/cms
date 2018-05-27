<?php
$tags['Header'] = 'Вопрос/ответ';
$tags['Add_CSS'].=';article_news_faq';
$tags['INCLUDE_HEAD'].='<link href="article_news_faq.css" type="text/css" rel=stylesheet />'."\n";;
require_once '../include/common.php';

require_once $INC_DIR . 'lib_bbcode.php';
$editor = new BBCODE_EDITOR ();

$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";

$code_ok = 0;
if (isset($input['send_img_code'])) {
    if ($input['img_code'] == $_SESSION['IMG_CODE']) {
        $code_ok = 1;
    } else {
        $code_err = 1;
    }
}
$_SESSION['IMG_CODE'] = rand(111111, 999999);

if (isset($input['page'])) {
    $_SESSION['FAQ_PAGE'] = $input['page'];
}

if (!isset($_SESSION["FAQ_PAGE"]))$_SESSION['FAQ_PAGE'] = 1;

$TABLE = 'faq';
$MSG_PER_PAGE = $settings['faq_msg_per_page'];

if (!count($input))$list = 1;

if ($input['added']) {
    $err = 0;
    $input['form'][txt] = $editor->GetValue();
    if (strlen($input['form']['author']) < 3) {
        $content.=my_msg_to_str('form_error_name');
        $err = 1;
    } elseif (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input['form']['email'])) {
        $content.=my_msg_to_str('form_error_email');
        $err = 1;
    } elseif (strlen($input['form']['txt']) < 10) {
        $content.=my_msg_to_str('form_error_msg_too_short');
        $err = 1;
    } elseif (($code_err) || (!isset($input['send_img_code']))) {
        $content.=my_msg_to_str('form_error_code');
        if(!$settings['debug'])$err = 1;
    }
    if ($err) {
        $input['add'] = 1;
    } else {
        $input['form']['ip'] = $server["REMOTE_ADDR"];
        $input['form']['date'] = 'now()';
        // $input['form'][txt]=strip_tags($input['form'][txt],"<b><i><p><br>");
        $input['form']['txt'] = $editor->GetHTML();
        $query = "insert into {$TABLE} " . db_insert_fields($input['form']);
        $result = my_query($query);
        $message.='Автор: ' . $input['form']['author'] . "\n";
        $message.='E-Mail: ' . $input['form']['email'] . "\n";
        $message.='IP: ' . $input['form']['ip'] . "\n";
        $message.="Сообщение: \n";
        $message.=str_replace('\r\n',"\n",$input['form']['txt']) . "\n";
        send_mail($settings['email_to_addr'], 'На сайте http://' . $_SERVER['HTTP_HOST'] . $SUBDIR . ' оставлено новое сообщение.', $message);
        $list = 1;
    }
}

if ($input["add"]) {
    if (is_array($input['form'])) {
        $data = $input['form'];
        $tags = array_merge($tags, $data);
        $editor->SetValue($input['form']['txt']);
    }
    $tags[editor] = $editor->GetContol(400, 200, '../images/bbcode_editor');
    $content.=get_tpl_by_title('faq_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}

$query = "SELECT ceiling(count(id)/$MSG_PER_PAGE) from $TABLE where active='Y'";
$result = my_query($query, null, true);
list($PAGES) = $result->fetch_array();

if ($PAGES > 1) {
    $tags[pages_list] = "<center>";
    for ($i = 1; $i <= $PAGES; $i++)
        $tags[pages_list].=($i == $_SESSION["FAQ_PAGE"] ? "[ <b>$i</b> ]&nbsp;" : "[ <a href=" . $server["PHP_SELF"] . "?page=$i>$i</a> ]&nbsp;");
    $tags[pages_list].="</center>";
}

$limit = $MSG_PER_PAGE * ($_SESSION["FAQ_PAGE"] - 1);
$query = "SELECT id from $TABLE where active='Y' order by id desc limit $limit";
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array())
    $LAST_ID = $row[id];

$query = "SELECT $TABLE.* from $TABLE where $TABLE.active='Y'" . ($LAST_ID ? " and $TABLE.id<'$LAST_ID'" : "") . " group by $TABLE.id order by $TABLE.id desc limit $MSG_PER_PAGE";
$result = my_query($query, $conn, true);

if (!$result->num_rows) {
    $content.=my_msg_to_str("$part_empty");
} else {
    $content.=get_tpl_by_title("faq_list", $tags, $result);
}
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);