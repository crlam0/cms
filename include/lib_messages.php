<?php

/*
  Messages library.
 */

use Classes\App;

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
    return App::$message->get($title, $tags, $str);
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
    App::$message->mail($message_to, $subject, $message);
}

/**
 * Send SMS via sms.ru
 *
 * @param string $message Message content
 *
 */
function send_sms($message) {
    if(!strlen(App::$settings['sms_api_id'])){
        return false;
    }
    
    $ch = curl_init('http://sms.ru/sms/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'api_id' => App::$settings['sms_api_id'],
        'to' => App::$settings['sms_my_number'],
        'text' => $message
    ));
    $body = curl_exec($ch);    
    curl_close($ch);
    return $body;
}

