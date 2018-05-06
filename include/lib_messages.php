<?php

/*
  Messages library.
 */

/**
 * Return message by title
 *
 * @param string $title Message title
 * @param string $tags Array of tags
 * @param string $str Content string
 *
 * @return string Output string
 */
function my_msg_to_str($title, $tags = array(), $str = '') {
    global $conn, $settings;
    if (strlen($title)) {
        $sql = "select * from messages where title='{$title}'";
        $message = my_select_row($sql, 1);
    }
    if (strlen($str))
        $message[content] = $str;
    if (!strlen($message['content'])) {
        return '';
    }
    if (is_array($tags))
        foreach ($tags as $key => $value) {
            $message[content] = str_replace('[%' . $key . '%]', $value, $message[content]);
        }
    if ($message) {
        // return "<div align=center><div class=msg_{$message['type']} bgcolor=$bgcolor><font class={$message['type']}>{$message['content']}</font></div></div>";
        switch ($message['type']) {
            case 'info':
                $class='info';
                break;
            case 'notice':
                $class='warning';
                break;
            case 'error':
                $class='danger';
                break;           
            default:
                $class='success';                                
        }   
        return '<p class="alert normal-form alert-' . $class .'">' . $message['content'] . '</p>';
    }
}

/**
 * Print message by title
 *
 * @param string $title Message title
 * @param string $tags Array of tags
 * @param string $str Content string
 *
 */
function my_msg($title, $tags = array(), $str = '') {
    echo my_msg_to_str($title, $tags, $str);
}

/**
 * Print OK message
 *
 * @param string $string Message content
 *
 */
function print_ok($string) {
    my_msg('info', '', $string);
}

/**
 * Print ERROR message
 *
 * @param string $string Message content
 *
 */
function print_error($string) {
    my_msg('error', '', $string);
}

/**
 * Print DEBUG message
 *
 * @param string $string Message content
 *
 */
function print_debug($string) {
    global $settings;
    if ($settings['debug'])
        print("<center><font class=debug>{$string}</font></center>");
}

/**
 * Print array content
 *
 * @param array $array Input array
 *
 */
function print_array($array) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

/**
 * Add message to admin_log table
 *
 * @param string $string Message content
 *
 */
function admin_log($message) {
    $query = "insert into admin_log(user_id,date,msg) values('" . $_SESSION['UID'] . "',now(),'{$message}')";
    my_query($query);
}

/**
 * Send mail with header
 *
 * @param string $message_to Destination address
 * @param string $subject Message subject
 * @param string $message Message content
 *
 */
function send_mail($message_to, $subject, $message) {
    global $settings;
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=windows-1251\r\n";
    $headers .= "From: {$settings['email_from_addr']}\r\n";
    $subject = iconv('UTF-8', 'windows-1251', $subject);
    $message = iconv('UTF-8', 'windows-1251', $message);
    mail($message_to, $subject, $message, $headers);
}

/**
 * Send mail from template
 *
 * @param string $tpl_title Template title
 * @param string $message_to Destination address
 * @param string $subject Message subject
 * @param array $tags Array of tags
 *
 */
function my_send_mail($tpl_title, $message_to, $subject, $tags) {
    $message = get_tpl_by_title($tpl_title, $tags);
    send_mail($message_to, $subject, $message);
}

/**
 * Send SMS via sms.ru
 *
 * @param string $message Message content
 *
 */
function send_sms($message) {
    global $settings;
    if(!strlen($settings['sms_api_id'])){
        return 0;
    }
    
    $ch = curl_init('http://sms.ru/sms/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'api_id' => $settings['sms_api_id'],
        'to' => $settings['sms_my_number'],
        'text' => $message
    ));
    $body = curl_exec($ch);    
    curl_close($ch);
    return $body;
}

