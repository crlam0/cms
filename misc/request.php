<?php

include '../include/common.php';

if(strlen($settings['banned_addr'])) {
    $banned_array = explode(';', $settings['banned_addr']);
    if(is_array($banned_array)) {
        foreach($banned_array as $addr) {
            if( strlen($addr) && strstr($server['REMOTE_ADDR'],$addr) ) {
                echo 'Banned IP';
                exit();
            }
        }
    }    
}

if ($input['request']=='top') {
    $err = 0;
    if ((strlen($input['firstname']) < 2) && ($input['firstname']!=='undefined')) {
        echo 'Вы не ввели свое имя !';
        exit;
    }
    if (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input['phone'])) {
        echo 'Неверный номер телефона ! Формат: +7-xxx-xxx-xxxx или xxx-xx-xx';
        exit;
    }
    if (strlen($input['code'])<1) {
        echo 'Вы не ввели код проверки';
        exit;
    }
    if ($input['code'] != $_SESSION['IMG_CODE']) {
        echo 'Неверный код проверки';
        exit;
    }
    /*
    if ( (!is_numeric($input['type_id'])) || ($input['type_id']<1) ) {
        echo 'Вы не выбрали тип услуги';
        exit;
    } else {
        list($item_list) = my_select_row("select name from request_types where id='{$input['type_id']}'");
    } 
     * 
     */
    if (strlen($input['comment'])<3) {
        echo 'Ваше сообщение слишком короткое';
        exit;
    }
    $input['comment'] = "Заявка через верхнюю форму: \n" . $input['comment'];
    $contact_info = 'Имя: ' . $input['firstname'] . "\n";
    $contact_info.= 'Телефон: ' . $input['phone'] . "\n";
    $contact_info.= 'IP адрес: ' . $server['REMOTE_ADDR'] . "\n";    
}

if ($input['request']=='call') {
    $err = 0;
    if ((strlen($input['firstname']) < 2) && ($input['firstname']!=='undefined')) {
        echo 'Вы не ввели свое имя !';
        exit;
    }
    if (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input['phone'])) {
        echo 'Неверный номер телефона ! Формат: +7-xxx-xxx-xxxx или xxx-xx-xx';
        exit;
    }
    if (strlen($input['code'])<1) {
        echo 'Вы не ввели код проверки';
        exit;
    }
    if ($input['code'] != $_SESSION['IMG_CODE']) {
        echo 'Неверный код проверки';
        exit;
    }
    $contact_info = 'Имя: ' . $input['firstname'] . "\n";
    $contact_info.= 'Телефон: ' . $input['phone'] . "\n";
    $contact_info.= 'IP адрес: ' . $server['REMOTE_ADDR'] . "\n";    
    $input['comment'] = 'Заказ звонка';
}

if ($input['request']=='form') {
    $err = 0;
    // print_array($input);
    // print_array($_FILES);
    
    if ((strlen($input['request-form-name']) < 2) || ($input['request-form-name']==='undefined')) {
        echo '<p class="alert alert-warning">Вы не ввели свое имя !</p>';
        exit;
    }
    if (!preg_match("/^\+?[\d\(\)-]{7,20}$/", $input['request-form-phone'])) {
        echo '<p class="alert alert-warning">Неверный номер телефона ! Формат: +7-xxx-xxx-xxxx или xxx-xx-xx</p>';
        exit;
    }
    if(!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input['request-form-email'])){
        echo '<p class="alert alert-warning">Неверный адрес E-Mail</p>';
        exit;
    }
    if (strlen($input['request-form-code'])<1) {
        echo '<p class="alert alert-warning">Вы не ввели код проверки</p>';
        exit;
    }
    if ($input['request-form-code'] != $_SESSION['IMG_CODE']) {
        echo '<p class="alert alert-warning">Неверный код проверки</p>';
        exit;
    }
    if (strlen($input['request-form-comment'])<3) {
        echo '<p class="alert alert-success">Ваше сообщение слишком короткое</p>';
        exit;
    }
    $input['comment'] = "Заявка через нижнюю форму: \n" . $input['request-form-comment'];
    if($_FILES['request-form-file']['size']>0) {
        $f_info = pathinfo($_FILES['request-form-file']['name']);
        if( !in_array($f_info['extension'], explode(',',$settings['files_upload_extensions'])) ){
            echo '<p class="alert alert-success">Неерный тип файла</p>';
            exit;
        }
        $file_name = encodestring($f_info['filename']) . '.' . $f_info['extension'];
        if ( !move_uploaded_file($_FILES['request-form-file']['tmp_name'], $DIR . $settings['files_upload_path'] . $file_name)) {
            echo '<p class="alert alert-success">Ошибка загрузки файла</p>';
            exit;
        }
        $data['file_name'] = $file_name;
        $mail_attach = "\n\n".'Прикрепленный файл: '. $server['HTTP_HOST'] . $BASE_HREF . $settings['files_upload_path'] . $file_name;
    }
    
    $contact_info = 'Имя: ' . $input['request-form-name'] . "\n";
    $contact_info.= 'E-Mail: ' . $input['request-form-email'] . "\n";
    $contact_info.= 'Телефон: ' . $input['request-form-phone'] . "\n";
    $contact_info.= 'IP адрес: ' . $server['REMOTE_ADDR'] . "\n";
    
}

if(strlen($contact_info)){
    $data['date'] = 'now()';
    $data['contact_info'] = $contact_info;
    $data['item_list'] = $item_list;
    $data['comment'] = $input['comment'];
    $query = 'insert into request' . db_insert_fields($data);
    my_query($query, $null, true);

    $message = $item_list . "\n\n" . $contact_info . "\n\n" . $input['comment'] . $mail_attach;
    
    send_mail($settings['email_to_addr'], 'Заказ с сайта ' . $server['HTTP_HOST'] . $BASE_HREF, $message);
    // send_mail('79139000568@sms.mtslife.ru', 'Заказ с сайта ' . $server['HTTP_HOST'] . $BASE_HREF, $message);
    
    $_SESSION['IMG_CODE'] = rand(111111, 999999);
        
    echo 'ok';
    exit;
}