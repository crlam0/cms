<?php
if(!isset($input)) {
    require '../../include/common.php';
}
$tags['Header'] = 'Вопрос/ответ';
$tags['INCLUDE_CSS'].='<link href="'.$SUBDIR.'css/article_news_faq.css" type="text/css" rel=stylesheet />'."\n";;

use Classes\Pagination;
use Classes\BBCodeEditor;
$editor = new BBCodeEditor ();

$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";
add_nav_item($tags['Header']);

$code_ok = 0;
if (isset($input['send_img_code'])) {
    if ( array_key_exists('IMG_CODE', $_SESSION) && $input['img_code'] == $_SESSION['IMG_CODE']) {
        $code_ok = 1;
    } else {
        $code_err = 1;
    }
}
$_SESSION['IMG_CODE'] = rand(111111, 999999);

if(strstr($input['uri'],'page')){
    $input['page']=str_replace('page','',$input['uri']);
}else{
    $input['page']=1;
}

if (isset($input['page'])) {
    $_SESSION['FAQ_PAGE'] = $input['page'];
}

$TABLE = 'faq';
$MSG_PER_PAGE = $settings['faq_msg_per_page'];

if (!$input->count()){
    $list = 1;
}

if ($input['added']) {
    $err = false;
    $input['form']['txt'] = $editor->GetValue();
    if (!check_csrf_token()) {
        $content.=my_msg_to_str('error', [] ,'CSRF Error');
        $err = true;
    } elseif (strlen($input['form']['author']) < 3) {
        $content.=my_msg_to_str('form_error_name');
        $err = true;
    } elseif (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input['form']['email'])) {
        $content.=my_msg_to_str('form_error_email');
        $err = true;
    } elseif (strlen($input['form']['txt']) < 10) {
        $content.=my_msg_to_str('form_error_msg_too_short');
        $err = true;
    } elseif (strlen($input['form']['txt']) > 512) {
        $content.=my_msg_to_str('form_error_msg_too_long');
        $err = true;
    } elseif (($code_err) || (!isset($input['send_img_code']))) {
        $content.=my_msg_to_str('form_error_code');
        if(!$settings['debug']){
            $err = true;
        }
    }
    if ($err) {
        $input['add'] = true;
    } else {
        $input['form']['ip'] = $server["REMOTE_ADDR"];
        $input['form']['date'] = 'now()';
        // $input['form'][txt]=strip_tags($input['form'][txt],"<b><i><p><br>");
        $input['form']['txt'] = $editor->GetHTML();
        $query = "insert into {$TABLE} " . db_insert_fields($input['form']);
        $result = my_query($query);
        $message='Автор: ' . $input['form']['author'] . "\n";
        $message.='E-Mail: ' . $input['form']['email'] . "\n";
        $message.='IP: ' . $input['form']['ip'] . "\n";
        $message.="Сообщение: \n";
        $message.=str_replace('\r\n',"\n",$input['form']['txt']) . "\n";
        if(!$settings['debug']){
            send_mail($settings['email_to_addr'], 'На сайте http://' . $_SERVER['HTTP_HOST'] . $SUBDIR . ' оставлено новое сообщение.', $message);
        }
        $list = 1;
    }
}

if ($input['add']) {
    if (is_array($input['form'])) {
        $data = $input['form'];
        $tags = array_merge($tags, $data);
        $editor->SetValue(stripcslashes($input['form']['txt']));
    }
    $tags['editor'] = $editor->GetContol(400, 200, '../images/bbcode_editor');
    $content.=get_tpl_by_title('faq_edit_form', $tags);
    echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
    exit;
}

$query = "SELECT count(id) from $TABLE where active='Y'";
$result = my_query($query, true);
list($total) = $result->fetch_array();

$pager = new Pagination($total,$_SESSION["FAQ_PAGE"],$MSG_PER_PAGE);
$tags['pager'] = $pager;

$query = "SELECT $TABLE.* from $TABLE where $TABLE.active='Y' group by $TABLE.id order by $TABLE.id desc limit {$pager->getOffset()},{$pager->getLimit()}";
$result = my_query($query, true);

if (!$result->num_rows) {
    $content.=my_msg_to_str('part_empty');
} else {
    $content.=get_tpl_by_title('faq_list', $tags, $result);
}
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);

