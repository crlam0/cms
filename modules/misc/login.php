<?php
if(!isset($input)) {
    require '../../include/common.php';
}

use Classes\App;

$tags['Header'] = 'Вход в систему';
$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";
add_nav_item($tags['Header']);

$content = '';

if (isset($input) && $input['logon']) {
    if($row = App::$user->authByLoginPassword($input['login'],$input['passwd'])) {
        $_SESSION['UID']=App::$user->id;
        $_SESSION['FLAGS']=App::$user->flags;
        if($input['rememberme']) {
            App::$user->setRememberme(App::$user->id,$COOKIE_NAME);
        }            
        if (mb_strlen($row['salt']) !== 22) {
            $content .= my_msg_to_str('notice','','Ваш пароль устарел. Пожалуйста, поменяйте его на другой <a href="'.App::$SUBDIR.'passwd_change/" />по этой ссылке</a> ');
            echo get_tpl_by_name($part['tpl_name'], $tags, '', $content);
            exit;
        }
        if (strlen($_SESSION['GO_TO_URI'])) {
            $uri=$_SESSION['GO_TO_URI'];
            unset($_SESSION['GO_TO_URI']);
            redirect($uri);
        } else {
            redirect(App::$SUBDIR);
        }
        exit;            
    }
    $content .= my_msg_to_str('user_login_failed');
}

if (!App::$user->id) {
    if(isset($input['login'])) {
        $tags['login'] = $input['login'];
    }
    $content.=get_tpl_by_name('user_login_promt', $tags);
} else {
    $content = my_msg_to_str('user_already_logged_on');
}
echo get_tpl_default($tags, '', $content);

