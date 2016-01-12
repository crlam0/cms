<?php

$tags[Header] = 'Вход в систему';
include '../include/common.php';
$tags[nav_str].="<span class=nav_next>{$tags[Header]}</span>";

if ($_POST["logon"]) {
    $query = "SELECT id,flags,passwd FROM users WHERE login='" . db_test_param($_POST["login"]) . "' and flags like '%active%'";
    $result = my_query($query, $conn, true);
    if ($result->num_rows) {
        $row = $result->fetch_array();
        if(strcmp(md5(db_test_param($_POST['passwd'])),$row['passwd'])==0){
            $_SESSION['UID']=$row['id'];
            $_SESSION['FLAGS']=$row['flags'];
            if (strlen($_SESSION['GO_TO_URI'])) {
                header('Location: ' . $_SESSION['GO_TO_URI']);
                unset($_SESSION['GO_TO_URI']);
            } else {
                header('Location:' . $BASE_HREF);
            }
            exit();            
        }
    }    
    $content = my_msg_to_str('login_failed') . '<br><br>';
}

if (!$_SESSION["UID"]) {
    $tags[login] = $_POST['login'];
    $content.=get_tpl_by_title('login_promt', $tags);
    echo get_tpl_by_title("$part[tpl_name]", $tags, '', $content);
} else {
    $content = my_msg_to_str('login_already_logged_on');
    echo get_tpl_by_title($part[tpl_name], '', '', $content);
}
?>