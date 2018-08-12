<?php

$tags['Header'] = "Корзина";

use Classes\SummToStr;
$SummToStr = new SummToStr();

$tags['nav_str'].="<span class=nav_next>Корзина</span>";

$IMG_ITEM_PATH = $DIR . $settings['catalog_item_img_path'];
$IMG_ITEM_URL = $BASE_HREF . $settings['catalog_item_img_path'];

if (isset($input["clear"])) {
    unset($_SESSION["BUY"]);
    header("Location: buy.php");
    exit;
}

if (isset($input["add_buy"])) {
    while (list ($n, $item_id) = @each($input["buy_item"])) {
        $_SESSION["BUY"][$item_id]["count"] = 1;
    }
    header("Location: buy.php");
    exit;
}

if (isset($input["calc"])) {
    while (list ($n, $item_cnt) = @each($input["buy_cnt"]))
        if (is_numeric($item_cnt)) {
            $_SESSION["BUY"][$n]['count'] = $item_cnt;
            $_SESSION["BUY"][$n]["size"] = $input["buy_size"][$n];
            $_SESSION["BUY"][$n]["color"] = $input["buy_color"][$n];
        }
//        echo "<pre>";print_r($_SESSION["BUY"]);echo "</pre>";
}

function get_discount($summ){
        
        $query="SELECT discount from discount where summ<='$summ' order by summ desc";
        $result=my_query($query);
        if($result->num_rows){
                list($discount)=$result->fetch_array();
        }else{
                $discount=0;
        }
        return $discount;
}
function calc_discount($summ,$discount){
        return $summ*(1-$discount/100);
}


if ($input["get_summary_cost"]) {
    if (count($_SESSION["BUY"])) {
        foreach ($_SESSION["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " id='$item_id'" : " or id='$item_id'");
        }
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = my_query($query, true);
        $summ = 0;
        $cnt = 0;
        if ($result->num_rows) {
            while ($row = $result->fetch_array()) {
                $summ+=$row['price'] * $_SESSION["BUY"][$row['id']]["count"];
                $cnt+=$_SESSION["BUY"][$row['id']]["count"];
            }
        }    
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $item_list.="<br><b>Итого $cnt шт. на сумму " . add_zero($summ) . " руб.<br>\n";
        $item_list.="Скидка: " . get_discount($summ) . "%\n";
        $item_list.="Сумма с учетом скидки: " . add_zero($summ_with_discount) . " руб.</b><br>\n";
    }
    echo $item_list;
    exit();
}

if ($input["get_summary"]) {
    if (count($_SESSION["BUY"])) {
        $item_list="<div class=\"buy_summary\">
            <h3>Сейчас в корзине :</h3><br>";
        $where='';
        foreach ($_SESSION["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " id='$item_id'" : " or id='$item_id'");
        }
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = my_query($query, true);
        $summ = 0;
        $cnt = 0;
        if ($result->num_rows)
            while ($row = $result->fetch_array()) {
                $summ+=$row['price'] * $_SESSION["BUY"][$row['id']]["count"];
                $cnt+=$_SESSION["BUY"][$row['id']]["count"];
                $item_list.="$row[title]\t Кол-во:" . $_SESSION["BUY"][$row['id']]["count"] . "\t" .
                        " Цена: {$row['price']}<br>\n";
            }
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $item_list.="<br><b>Итого $cnt шт. на сумму " . add_zero($summ) . " руб.<br>\n";
        $item_list.="Скидка: " . get_discount($summ) . "%\n";
        $item_list.="Сумма с учетом скидки: " . add_zero($summ_with_discount) . " руб.</b><br>\n";
        $item_list.="</div>";
        $item_list.='<br /><a href='.$SUBDIR.'catalog/buy.php class="btn btn-success" type="button" id="request-button">Оформить заказ</a>';
    }
    echo $item_list;
    exit();
}

// ob_start();



if (isset($input["request_done"])) {
    $err = 0;
//        if(!preg_match("/^[\w-]+$/",$input["lastname"])){
    if (!isset($_SESSION["BUY"]) || !count($_SESSION["BUY"])) {
        $content.=my_msg_to_str('error',[],"Нет товаров в корзине");
        $err = 1;       
    }
    if (strlen($input["lastname"])<3) {
        $content.=my_msg_to_str('error',[],"Неверно заполнено поле \"Фамилия\"");
        $err = 1;       
    }
//        if(!preg_match("/^[\w-]+$/",$input["firstname"])){
    if (strlen($input["firstname"])<3) {
        $content.=my_msg_to_str('error',[],"Неверно заполнено поле \"Имя\"");
        $err = 1;
    }
    if (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input["email"])) {
        $content.=my_msg_to_str('error',[],"Анкета заполнена неверно ! Неверный адрес E-Mail");
        $err = 1;
    }
    if (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input['phone'])) {
        $content.=my_msg_to_str('error',[],"Анкета заполнена неверно ! Неверный номер мобильного телефона. Формат: +7-xxx-xxx-xxxx");
        $err = 1;
    }
    if ($err) {
        $input["request"] = 1;
    } else {
        $where = "";
        foreach ($_SESSION["BUY"] as $item_id => $cnt) {
            $where.=(!strlen($where) ? " id='$item_id'" : " or id='$item_id'");
        }
        $query = "select * from cat_item where $where order by b_code,title asc";
        $result = my_query($query, null, false);
        $summ = 0;
        $cnt = 0;
        $item_list = "";
        if ($result->num_rows ){
            while ($row = $result->fetch_array()) {
                $summ+=$row['price'] * $_SESSION["BUY"][$row['id']]["count"];
                $cnt+=$_SESSION["BUY"][$row['id']]["count"];
                $item_list.="Наименовние: $row[title]\t Кол-во:" . $_SESSION["BUY"][$row['id']]["count"] . "\t" .
                        " Цена: {$row['price']}\n";
            }
        }    
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $item_list.="Итого $cnt шт. на сумму " . add_zero($summ) . " руб.\n";
        $item_list.="Скидка: " . get_discount($summ) . "%\n";
        $item_list.="Сумма с учетом скидки: " . add_zero($summ_with_discount) . " руб.\n";
        $contact_info="ФИО: " . $input["lastname"] . " " . $input["firstname"] . " " . $input["middlename"] . "\n";
        $contact_info.="E-Mail: " . $input["email"] . "\n";
        $contact_info.="Телефон: " . $input["phone"] . "\n";
        $contact_info.="IP адрес: " . $server["REMOTE_ADDR"] . "\n";
        $msg = "Наименования:\n{$item_list}\n\nКонтактная информация:\n{$contact_info}\n\n";
        $query = "insert into request(date,item_list,contact_info,comment) values(now(),'" . $item_list . "','" . $contact_info . "','" .$input['comment']."')";
        $result=my_query($query, true);
        
        unset($_SESSION['BUY']);
        
        // $msg = iconv('UTF-8', 'windows-1251', $msg);
        send_mail($settings["email_to_addr"], "Request from site " . $BASE_HREF, $msg);
        $content.=my_msg_to_str('',[],"Ваш заказ принят! В ближайшее время с Вами свяжется наш менеджер для подтверждения  и уточнения по замене, если на данный период времени некоторые позиции отсутствуют.");
       
        echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);
        exit();
    }
}


if (isset($_SESSION["BUY"]) && is_array($_SESSION["BUY"]) && count($_SESSION["BUY"])) {
    $where = "";
    $count = 0;
    foreach ($_SESSION["BUY"] as $item_id => $cnt) {
        $where.=(!strlen($where) ? " cat_item.id='$item_id'" : " or cat_item.id='$item_id'");
        $count = $count + (int)$cnt;
    }
    $query = "select cat_item.*,fname from cat_item left join cat_item_images on (cat_item_images.id=default_img) where $where order by b_code,title asc";
    $result = my_query($query, true);
    if ((isset($input["request_x"])) || (isset($input["request"]))) {
        $tags['Header'] = "Оформить заказ";
        $content.= '<div align="center" style="max-width: 400px;margin-top:30px;">' . "<form action=" . $_SERVER["PHP_SELF"] . ' method=post>
            <input type=hidden name=request_done value=1>          
            
            '.
            '<input type=edit size=32 maxlength=64 name=lastname class="form-control form-normal" placeholder="Фамилия" value="' . $input["lastname"] . '" required><br />'.
            '<input type=edit size=32 maxlength=64 name=firstname class="form-control" placeholder="Имя" value="' . $input["firstname"] . '" required><br />'.
            '<input type=edit size=32 maxlength=64 name=phone class="form-control" placeholder="Телефон" value="' . $input["phone"] . '" required><br />'.
            '<input type=edit size=32 maxlength=64 name=email class="form-control" placeholder="Адрес E-Mail" value="' . $input["email"] . '" required><br />'.
            '<textarea name=comment class="form-control" placeholder="Ваш коментарий" rows="8">'.$input["comment"].'</textarea><br />'.
            '     
            <input type=submit class="btn btn-success" value="  Отправить  ">
            </table>
            </form></div>
            ';
    } else {
        $content.= "
                <center>
                <form action=" . $server["PHP_SELF"] . " method=post name=request_form>
                <input type=hidden name=calc value=1>
                <table width=100% border=0 cellspacing=17 cellpadding=17 align=center>";
        $summ = 0;
        $cnt = 0;
        while ($row = $result->fetch_array()) {
            $summ+=$row['price'] * $_SESSION["BUY"][$row['id']]["count"];
            $cnt+=$_SESSION["BUY"][$row['id']]["count"];
            $content.= "<tr valign=middle>
                <td align=center width=50%>" . (file_exists($IMG_ITEM_PATH) . $row['fname'] ? "<img src=\"{$SUBDIR}catalog/image.php?id={$row['default_img']}&windowHeight=500&fix_size=1\">" : "&nbsp;") . "</td>
                <td class=price><b>$row[title]</b> &nbsp;&nbsp;(Кол-во: {$_SESSION["BUY"][$row['id']]["count"]})<br>
                Цена: <b>" . add_zero($row['price']) . " руб.</b><br>" . nl2br($row['descr']) . "<br>
                <input type=hidden name=buy_cnt[{$row['id']}] value=1>
                </td>
                </tr>
                ";
        }
        $summ_with_discount = calc_discount($summ, get_discount($summ));
        $content.= "
            <tr><td colspan=2>
            <center>Итого на сумму <b>" . add_zero($summ) . " руб.</b>
            " . (get_discount($summ) ? " С учетом скидки <b>" . get_discount($summ) . "%</b> сумма составлет: <b>" . add_zero($summ_with_discount) . "</b>" : "") . "
            </center><center>Итого к оплате: <b>" . $SummToStr($summ_with_discount) . "</b></center>
            </td></tr>    
            </table>
            <br />            
            <a onClick=\"document.request_form.submit();\" style=\"cursor: pointer\" class=\"btn btn-success\"> Посчитать </a>
            <a href=" . $server["PHP_SELF"] . "?request=1 class=\"btn btn-success\"> Оформить заказ </a>
            <a href=" . $server["PHP_SELF"] . "?clear=1 class=\"btn btn-success\"> Очистить список </a>
            ";
    }
} else {
    $content.=my_msg_to_str('notice',[],"Корзина пуста !");
}


echo get_tpl_by_title($part['tpl_name'], $tags, '', $content);


