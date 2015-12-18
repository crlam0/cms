<?php
include "../include/common.php";

if(isset($input['get_form_content'])){
    $tags['item_id']=$input['item_id'];
    echo get_tpl_by_title("cat_request_form",$tags);
}

if(isset($input['send_form'])){
    $error=false;
    $content="";
    if (!strlen($input["message"])) {
        $error_text[] = "Вы не ввели сообщение";
        $error = true;
    }
/*
    if (!strlen($input["firstname"])) {
        $error_text[] = "Неверно заполнено поле \"Имя\"";
        $error = true;
    }
 */
    if ((!strlen($input["phone"])) || (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input["phone"]))) {
        $error_text[] = "Неверный номер телефона";
        $error = true;
    }
    if($error){
        foreach($error_text as $value){
            $content.="<font class=error>{$value}</font><br />";
        }
        $content .= get_tpl_by_title("cat_request_form",$input);
    }else{
        $contact_info.="Имя: " . $input["firstname"] . "\n";
        $contact_info.="E-Mail: " . $input["email"] . "\n";
        $contact_info.="Телефон: " . $input["phone"] . "\n";
        $contact_info.="IP адрес: " . $server["REMOTE_ADDR"] . "\n";

        list($item_title,$item_address) = my_select_row("select title,address from cat_item where id='{$input['item_id']}'", 1);
        $contact_info.="Квартира: {$item_title},{$item_address}\n";
        
        $query = "insert into request(date,contact_info,message) values(now(),'{$contact_info}','{$input["message"]}')";
        my_query($query, $conn);

        $msg = $contact_info."\n\n".$input["message"];
        $msg = iconv('UTF-8', 'windows-1251', $msg);
        send_mail($settings["request_to_email"], "Request from site " . $BASE_HREF, $msg);
        print_ok("Ваше сообщение принято ! В ближайшее время с Вами свяжется наш менеджер.");

    }
    echo $content;
}


?>

