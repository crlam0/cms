<?php

$tags[Header] = "Вход в систему";
include "../include/common.php";
$tags[nav_str].="<span class=nav_next>$tags[Header]</span>";

if ($_POST["logon"]) {
    $query = "SELECT id,flags,passwd FROM users WHERE login='" . db_test_param($_POST["login"]) . "' and flags like '%active%'";
    $result = my_query($query, $conn);
    $num = $result->num_rows;
    if ($num) {
        $row = $result->fetch_array();
        if(strcmp(md5(db_test_param($_POST["passwd"])),$row["passwd"])==0){
            $session["UID"]=$row["id"];
            $session["FLAGS"]=$row["flags"];
            if (strlen($session["GO_TO_URI"])) {
                header("Location: " . $session["GO_TO_URI"]);
                unset($session["GO_TO_URI"]);
            } else {
                header("Location: $BASE_HREF");
            }
            exit();            
        }
    }    
    $content = my_msg_to_str("login_failed") . "<br><br>";
}

if (!$session["UID"]) {
    $tags[login] = $_POST["login"];
    $content.=get_tpl_by_title("login_promt", $tags);
    echo get_tpl_by_title("$part[tpl_name]", $tags, "", $content);
} else {
    $content = my_msg_to_str("login_already_logged_on");
    echo get_tpl_by_title("$part[tpl_name]", "", "", $content);
}
?>