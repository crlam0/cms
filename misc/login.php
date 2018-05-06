<?php

$tags['Header'] = 'Вход в систему';
include '../include/common.php';
$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";

if ($input['logon']) {
    $query = "select id,flags,passwd,salt from users where login='" . $input['login'] . "' and flags like '%active%'";
    $result = my_query($query, $conn, false);
    if ($result->num_rows) {
        $row = $result->fetch_array();        
	echo $row['salt'];
        if(strcmp(user_encrypt_password($input['passwd'], $row['salt']),$row['passwd'])==0){
	echo $row['salt'];
            $_SESSION['UID']=$row['id'];
            $_SESSION['FLAGS']=$row['flags'];
            if (mb_strlen($row['salt']) !== 22) {
                $content .= my_msg_to_str('notice','','Ваш пароль устарел. Пожалуйста, поменяйте его на другой <a href="'.$BASE_HREF.'passwd_change/" />по этой ссылке</a> ');
                echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
                exit;
            }
            if (strlen($_SESSION['GO_TO_URI'])) {
                header('Location: ' . $_SESSION['GO_TO_URI']);
                unset($_SESSION['GO_TO_URI']);
            } else {
                header('Location:' . $BASE_HREF);
            }
            exit;            
        }
    }    
    $content .= my_msg_to_str('user_login_failed');
}

if (!$_SESSION['UID']) {
    $tags['login'] = $input['login'];
    $content.=get_tpl_by_title('user_login_promt', $tags);
} else {
    $content = my_msg_to_str('user_already_logged_on');
}
echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);

