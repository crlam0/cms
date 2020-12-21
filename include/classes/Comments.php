<?php


namespace classes;

use classes\App;
use classes\BBCodeEditor;

/**
 * Add coments to some content
 *
 * @return string Output string
 */

class Comments
{
    private $__target_type;
    private $__target_id;
    private $__editor;
    private $__code_ok;
    private $__new_form;
    private $__table = 'comments';
    private $__get_form_data_result = '';
    
    /**
     * Set object params
     *
     * @param integer $target_type Target type
     * @param integer $target_id Target ID
     * @param string $action_href Action HREF
     *
     */
    public function __construct(string $target_type, int $target_id = 0, string $action_href = '')
    {
        $this->__target_type=$target_type;
        $this->__target_id=$target_id;
        $this->__editor = new BBCodeEditor ();
        $this->__new_form = true;
    }

    /**
     * Show textarea with BBCode controls.
     *
     * @param integer $target_id ID of content
     *
     * @return integer Count of comments
     */
    public function show_count(int $target_id) : string
    {        
        $query="select count(id) from {$this->__table} where active='Y' and target_type='{$this->__target_type}' and target_id='{$target_id}'";
        list($count) = App::$db->getRow($query);
        return $count;
    }
    
    /**
     * Show comments list
     *
     * @param array $tags Array of tags
     *
     * @return string Output content
     */
    public function show_list(array $tags = []) : string 
    {        
        $query="select * from {$this->__table} where active='Y' and target_type='{$this->__target_type}' and target_id='{$this->__target_id}' order by id asc";
        $result = App::$db->query($query);
        return App::$template->parse('comments_list',$tags,$result);        
    }
    
    /**
     * Show form for comments adding
     *
     * @param array $tags Array of tags
     *
     * @return string Output content
     */
    public function show_form(array $tags = []) : string 
    {
        global $_SESSION;
        if ( $this->__new_form ) {
            $this->__editor->SetValue('');
        } elseif (is_array(App::$input['form'])) {
            $data = App::$input['form'];
            $tags = array_merge($tags, $data);            
        }
        
        $tags['editor'] = $this->__editor->GetContol(400, 200, App::$SUBDIR . 'theme/bbcode_editor');
        if(!isset($tags['action'])){
            $tags['action'] = App::$server['PHP_SELF'];        
        }
        $_SESSION['IMG_CODE'] = rand(111111, 999999);        
        return $this->__get_form_data_result . App::$template->parse('comment_add_form', $tags);
    }
    
    private function checkInput(array $input) : array 
    {
        $err = false;
        $output = '';
        if (!check_csrf_token()) {
            $output.=App::$message->get('error', [] ,'CSRF Error');
            $err = true;
        } elseif (!isset($input['author']) || strlen($input['author']) < 3) {
            $output.=App::$message->get('form_error_name');
            $err = true;
        } elseif (!isset($input['email']) || !preg_match('/^[A-Za-z0-9-_]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/', $input['email'])) {
            $output.=App::$message->get('form_error_email');
            $err = true;
        } elseif (strlen($this->__editor->GetValue()) < 10) {
            $output.=App::$message->get('form_error_msg_too_short');
            $err = true;
        } elseif (strlen($this->__editor->GetValue()) > 512) {
            $output.=App::$message->get('form_error_msg_too_long');
            $err = true;
        } elseif ( ($input['img_code'] != $_SESSION['IMG_CODE']) && (!$settings['debug']) ) {
            $output.=App::$message->get('form_error_code');
            $err = true;
        }
        return [$err, $output];
    }

    /**
     * Parse form data
     *
     * @param array $input Input array
     *
     */
    public function get_form_data($input)
    {
        if(!isset($input) || !$input['add_comment']) {
            return false;
        };
        list($err, $output) = $this->checkInput($input);
        if ( $err ) {
            $this->__new_form = false;
        } else {
            $input['ip'] = App::$server['REMOTE_ADDR'];
            $input['date_add'] = 'now()';
            $input['uid'] = App::$user->id;
            $input['target_type']=$this->__target_type;
            $input['target_id']=$this->__target_id;
            $input['content']=$this->__editor->GetHTML();
            unset($input['add_comment']);
            unset($input['img_code']);
            $query = "insert into {$this->__table} " . db_insert_fields($input);
            App::$db->query($query);
            $output.=App::$message->get('',[],'Комментарий успешно добавлен');

            $remote_host=(check_key('REMOTE_HOST',App::$server) ? App::$server['REMOTE_HOST'] : gethostbyaddr(App::$server['REMOTE_ADDR']) );
            $message="Автор: {$input['author']} ( {$input['email']} )\n";
            $message.="IP: {$input['ip']} ( {$remote_host} )\n";
            $message.="Сообщение:\n";
            $message.=str_replace('\r\n',"\n",$input['content']) . "\n";
            if(!App::$debug){
                App::$message->mail(App::$settings['email_to_addr'], 'На сайте http://'.App::$server['HTTP_HOST'].App::$SUBDIR.' оставлен новый комментарий.', $message);
            }    

            $this->__new_form = true;
        }
        $this->__get_form_data_result = $output;
    }
}


