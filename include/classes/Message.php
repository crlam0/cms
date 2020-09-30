<?php

namespace classes;
use classes\App;
use Swift;

class Message 
{    
    private function getClass(array $message) : string 
    {
        if(!array_key_exists('type', $message)) {
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
    
    private function parseTags(array $message, array $tags) : array {
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
    public function get(string $name, array $tags = [], string $content = '') : string 
    {
        if (strlen($name)) {
            $sql = "select * from messages where name='{$name}'";
            $message = App::$db->getRow($sql);
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
     * Return error message
     *
     * @param string $string Message content
     *
     */
    function getError(string $string) : string 
    {
        return $this->get('error', [], $string);
    }    
    
    /**
     * Print error message
     *
     * @param string $string Message content
     *
     */
    function error(string $string) : void 
    {
        echo $this->getError($string);
    }    
    
    /**
     * Return error messages from array
     *
     * @param string $string Message content
     *
     */
    function getErrorsFromArray(array $errors) : string
    {
        $content = '';
        foreach($errors as $error) {
            $content .= $this->getError($error);
        }
        return $content;
    }    
    
    /**
     * Print error messages from array
     *
     * @param string $string Message content
     *
     */
    function errorsFromArray(array $errors) : void 
    {
        echo $this->getErrorsFromArray($errors);
    }    

    /**
     * Add message to admin_log table
     *
     * @param string $message Message content
     *
     */
    function adminLog(string $message) : void
    {
        $query = "insert into admin_log(user_id,date,msg) values('" . App::$user->id . "',now(),'{$message}')";
        App::$db->query($query);
    }
    
    /**
     * Send mail with header
     *
     * @param string $message_to Destination address
     * @param string $subject Message subject
     * @param string $content Message content
     *
     */
    function mail(string $message_to, string $subject, string $content, string $content_type = 'text/plain') 
    {
        $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message($subject))
            ->setFrom(App::$settings['email_from_addr'])
            ->setTo($message_to)
            ->setBody($content, $content_type, 'utf-8')
            ;
        return $mailer->send($message);
    }    
    
}
