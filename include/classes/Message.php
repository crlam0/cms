<?php

namespace Classes;
use Classes\App;
use Swift;

class Message {
    
    private function getClass($message){
        if(!array_key_exists('type',$message)) {
            return 'success';
        }
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
        return $class;
    }
    
    private function parseTags($message, $tags) {
        if (is_array($tags)){
            if(array_key_exists('type',$tags)) {
                $message['type'] = $tags['type'];            
            }
            foreach ($tags as $key => $value) {
                if (is_string($value)){
                    $message['content'] = str_replace('[%' . $key . '%]', $value, $message['content']);
                }
            }
        }
        return $message;
    }
    
    /**
     * Return message by name
     *
     * @param string $name Message name
     * @param string $tags Array of tags
     * @param string $content Content string
     *
     * @return string Output string
     */
    public function get($name, $tags = array(), $content = '') {
        if (strlen($name)) {
            $sql = "select * from messages where name='{$name}'";
            $message = App::$db->select_row($sql, 1);
        }
        if (strlen($content)){
            $message['content'] = $content;
        }    
        if (!strlen($message['content'])) {
            $message['content'] = $name;
        }
        $message = $this->parseTags($message, $tags);
 
        if ($message) {
            return '<p class="alert normal-form alert-' . $this->getClass($message) . '">' . $message['content'] . '</p>';
        }
    }
    
    /**
     * Print error message
     *
     * @param string $string Message content
     *
     */
    function error($string) {
        echo $this->get->error('error',[],$string);
    }    
    
    /**
     * Add message to admin_log table
     *
     * @param string $message Message content
     *
     */
    function adminLog($message) {
        $query = "insert into admin_log(user_id,date,msg) values('" . App::$user->id . "',now(),'{$message}')";
        App::$db->query($query);
    }
    
    /**
     * Send mail with header
     *
     * @param string $message_to Destination address
     * @param string $subject Message subject
     * @param string $message Message content
     *
     */
    function mail($message_to, $subject, $message) {
        $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message($subject))
            ->setFrom(App::$settings['email_from_addr'])
            ->setTo($message_to)
            ->setBody($message)
            ;
        return $mailer->send($message);
    }    
}
