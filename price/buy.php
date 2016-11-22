<?php

$tags[Header] = "Заказ";
include_once "../include/common.php";
include "../inc/summ_to_str.php";

// mb_internal_encoding('UTF-8');

$IMG_ITEM_PATH = $DIR . $settings[catalog_item_img_path];
$IMG_ITEM_URL = $BASE_HREF . $settings[catalog_item_img_path];

if (isset($_GET["clear"])) {
    unset($_SESSION["BUY"]);
    header("Location: buy.php");
    exit;
}

if (isset($_POST["add_buy"])) {
    while (list ($n, $item_id) = @each($_POST["buy_item"])) {
        $_SESSION["BUY"][$item_id]["count"] = 1;
    }
    header("Location: buy.php");
    exit;
}

if (isset($_POST["calc"])) {
    while (list ($n, $item_cnt) = @each($_POST["buy_cnt"]))
        if (is_numeric($item_cnt)) {
            $_SESSION["BUY"][$n][count] = $item_cnt;
            $_SESSION["BUY"][$n]["size"] = $_POST["buy_size"][$n];
            $_SESSION["BUY"][$n]["color"] = $_POST["buy_color"][$n];
        }
//        echo "<pre>";print_r($_SESSION["BUY"]);echo "</pre>";
}

if ($input["get_summary"]) {
    if (count($_SESSION["BUY"])) {
        $item_list.="<div class=\"buy_summary\">
            <h3>Сейчас Вы заказали :</h3><br>";
        foreach ($_SESSION["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " id='$item_id'" : " or id='$item_id'");
        }
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = my_query($query, null, true);
        $summ = 0;
        $cnt = 0;
        $price_add = '';
        if ($result->num_rows)
            while ($row = $result->fetch_array()) {
                $row['price_clean']=$row['price'];
                if(mb_strstr($row[price],'от ')){
                    $row['price_clean']=str_replace('от ','',$row[price]);
                    $price_add='от ';
                }    
                $row['price_clean']=str_replace(' ','',$row['price_clean']);
                $summ+=$row['price_clean'] * $_SESSION["BUY"][$row[id]]["count"];
                $cnt+=$_SESSION["BUY"][$row[id]]["count"];
                $item_list.="$row[title]\t ({$row['units']}) Кол-во:" . $_SESSION["BUY"][$row[id]]["count"] . "\t" .
                        " Цена: $row[price]<br>\n";
            }
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $item_list.="<br><b>Итого $cnt наименований на сумму " . $price_add. add_zero($summ) . " руб.<br>\n";
        $item_list.="Скидка: " . get_discount($summ) . "%\n";
        $item_list.="Сумма с учетом скидки {$price_add}: " . add_zero($summ_with_discount) . " руб.</b><br>\n";
        $item_list.="</div>";
    }
    echo $item_list;
    exit();
}

ob_start();

function get_discount($summ) {
    global $conn;
    $query = "SELECT discount from discount where summ<='$summ' order by summ desc";
    $result = my_query($query, $conn);
    if ($result->num_rows) {
        list($discount) = $result->fetch_array();
    } else {
        $discount = 0;
    }
    return $discount;
}

function calc_discount($summ, $discount) {
    return $summ * (1 - $discount / 100);
}

if (isset($_POST["request_done"])) {
    $err = 0;
//        if(!preg_match("/^[\w-]+$/",$_POST["firstname"])){
    if (!strlen($_POST["firstname"])) {
        print_error("Неверно заполнено поле \"Имя\"");
        $err = 1;
    }
    if ((strlen($_POST["phone"])) && (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $_POST["phone"]))) {
        print_error("Анкета заполнена неверно ! Неверный номер телефона");
        $err = 1;
    }
    if ($err) {
        $_GET["request"] = 1;
    } else {
        $where = "";
        if (count($_SESSION["BUY"])) {
            foreach ($_SESSION["BUY"] as $item_id => $cnt) {
                $where.=(!strlen($where) ? " id='$item_id'" : " or id='$item_id'");
            }
            $query = "select * from cat_item where $where order by b_code,title asc";
            $result = my_query($query, $conn);
            print_debug($query);
            $summ = 0;
            $cnt = 0;
            $item_list = "";
            if ($result->num_rows)
                while ($row = $result->fetch_array()) {
                    $row['price_clean']=$row['price'];
                    if(mb_strstr($row[price],'от ')){
                        $row['price_clean']=str_replace('от ','',$row[price]);
                        $price_add='от ';
                    }    
                    $row['price_clean']=str_replace(' ','',$row['price_clean']);
                    $summ+=$row['price_clean'] * $_SESSION["BUY"][$row[id]]["count"];
                    $cnt+=$_SESSION["BUY"][$row[id]]["count"];
                    $item_list.="Наименовние: $row[title]\t Кол-во:" . $_SESSION["BUY"][$row[id]]["count"] . "\t" .
                            " Цена: $row[price]\n";
                }
        }
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $item_list.="Итого $cnt шт. на сумму " . add_zero($summ) . " руб.\n";
        $item_list.="Скидка: " . get_discount($summ) . "%\n";
        $item_list.="Сумма с учетом скидки: " . add_zero($summ_with_discount) . " руб.\n";
        $contact_info.="Имя: " . $_POST["firstname"] . "\n";
        $contact_info.="E-Mail: " . $_POST["email"] . "\n";
        $contact_info.="Телефон: " . $_POST["phone"] . "\n";
        $contact_info.="Примечания: " . $_POST["comments"] . "\n";
        $contact_info.="IP адрес: " . $_SERVER["REMOTE_ADDR"] . "\n";
        $msg = "Наименования:\n$item_list\n\nКонтактная информация:\n$contact_info\n\n";
        $query = "insert into request(date,item_list,contact_info) values(now(),'" . $item_list . "','" . $contact_info . "')";
        print_debug($query);
        my_query($query, $conn);
        unset($_SESSION["BUY"]);
        $msg = iconv('UTF-8', 'windows-1251', $msg);
        send_mail($settings["request_to_email"], "Request from site " . $BASE_HREF, $msg);
        print_ok("Ваш заказ принят! В ближайшее время с Вами свяжется наш менеджер для подтверждения  и уточнения заказа.");
        $content = ob_get_contents();
        ob_end_clean();
        echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
        exit();
    }
}

if (count($_SESSION["BUY"])) {
    $where = "";
    $count = 0;
    foreach ($_SESSION["BUY"] as $item_id => $cnt) {
        $where.=(!strlen($where) ? " cat_item.id='$item_id'" : " or cat_item.id='$item_id'");
        $count = $count + cnt;
    }
    $query = "select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img) where $where order by b_code,title asc";
    $result = my_query($query, null, true);
    if ((isset($_POST["request_x"])) || (isset($_GET["request"]))) {
        echo "<form action=" . $_SERVER['PHP_SELF'] . " method=post>
                <input type=hidden name=request_done value=1>
                <table width=600 border=0 cellspacing=5 cellpadding=5  align=center class=add_buy>
                <tr><td colspan=2 align=center><b>Заполните анкету</b></td></tr>
                <tr><td>Имя:</td><td><input type=edit size=32 maxlength=64 name=firstname value=\"" . $_POST["firstname"] . "\"></td></tr>
                <tr><td>Примечания:</td><td><textarea name=comments rows=7 cols=35 maxlength=64000>" . $_POST["comments"] . "</textarea></td></tr>
                <tr><td>Телефон:</td><td><input type=edit size=32 maxlength=64 name=phone value=\"" . $_POST["phone"] . "\"></td></tr>
                <tr><td>Адрес E-Mail:</td><td><input type=edit size=32 maxlength=64 name=email value=\"" . $_POST["email"] . "\"></td></tr>
                <tr align=left><td align=center colspan=2>
                <input type=submit value=\"  Отправить  \">
                </td></tr>
                </table>
                </form>
                ";

        $query = "select content from article where id='172'";
        $result = my_query($query, $conn);
        list($comments) = $result->fetch_array();
        echo $comments;
    } else {
        echo "
                <center>
                <form action=" . $_SERVER['PHP_SELF'] . " method=post name=request_form>
                <input type=hidden name=calc value=1>
                <table width=100% border=0 cellspacing=17 cellpadding=17 align=center>";
        $summ = 0;
        $cnt = 0;
        $price_add='';
        while ($row = $result->fetch_array()) {
            $row['price_clean']=$row['price'];
            if(mb_strstr($row[price],'от ')){
                $row['price_clean']=str_replace('от ','',$row[price]);
                $price_add='от ';
            }
            $row['price_clean']=str_replace(' ','',$row['price_clean']);
            $summ+=$row['price_clean'] * $_SESSION["BUY"][$row[id]]["count"];
            $cnt+=$_SESSION["BUY"][$row[id]]["count"];
            echo "<tr valign=middle>
                        <td class=price><b>$row[title]</b> &nbsp;&nbsp;(Кол-во: {$_SESSION["BUY"][$row[id]]["count"]})<br>
                        Цена: <b>" . $row[price] . " руб.</b><br>" . nl2br($row[descr]) . "<br>
                        <input type=hidden name=buy_cnt[$row[id]] value=1>
                        </td>
                        </tr>
                        ";
        }
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        echo "
                <tr><td colspan=2>
                <center>Итого на сумму {$price_add}<b>" . add_zero($summ) . " руб.</b>
                " . (get_discount($summ) ? " С учетом скидки {$price_add}<b>" . get_discount($summ) . "%</b> сумма составлет{$price_add}: <b>" . add_zero($summ_with_discount) . "</b>" : "") . "
                </center><center>Итого к оплате {$price_add}: <b>" . summ_to_str($summ_with_discount) . "</b></center>
                </td></tr>    
                <tr><td colspan=2 align=center>
                <table border=0 cellspacing=7 cellpadding=7 align=center><tr align=center>
                <td width=33%><a class=button onClick=\"document.request_form.submit();\" style=\"cursor: pointer\"> Посчитать </a></td>
                <td width=33% nowrap><a href=" . $_SERVER['PHP_SELF'] . "?request=1 class=button> Оформить заказ </a></td>
                <td width=33% nowrap><a href=" . $_SERVER['PHP_SELF'] . "?clear=1 class=button> Очистить список </a></td>
                </tr></table></form>
                </td></tr>
                </table>
                </center>
                ";
    }
} else {
    print_ok("Вы пока ничего не заказали.");
}


$content = ob_get_contents();
ob_end_clean();
echo get_tpl_by_title($part[tpl_name], $tags, "", $content);
?>