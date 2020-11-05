<?php

namespace modules\misc;

use classes\BaseController;
use classes\App;
use classes\Pagination;
use classes\BBCodeEditor;

/**
 * Controller for FAQ page.
 *
 * @author BooT
 */
class FAQController extends BaseController 
{
    private $TABLE = 'faq';
    private $MSG_PER_PAGE = '20';
    private $editor;
    
    public function __construct()
    {
        if(isset(App::$settings['faq_msg_per_page'])) {
            $this->MSG_PER_PAGE = App::$settings['faq_msg_per_page'];
        }
        $this->title = isset(App::$settings['faq_header']) ? App::$settings['faq_header'] : 'Вопросы/ответы';
        $this->breadcrumbs[] = ['title'=>$this->title];
        
        $this->editor = new BBCodeEditor ();
    }
    
    public function actionIndex(int $page = 1) : string 
    {
        $query = "SELECT count(id) from {$this->TABLE} where active='Y'";
        $result = App::$db->query($query);
        list($total) = $result->fetch_array();


        $pager = new Pagination($total, $page, $this->MSG_PER_PAGE);
        $tags['pager'] = $pager;

        $query = "SELECT {$this->TABLE}.* from {$this->TABLE} where {$this->TABLE}.active='Y' group by {$this->TABLE}.id order by {$this->TABLE}.id desc limit {$pager->getOffset()},{$pager->getLimit()}";
        $result = App::$db->query($query);

        return App::$template->parse('faq_list', $tags, $result);        
    }
    
    private function checkInput(array $input)
    {
        global $_SESSION;
        if (!check_csrf_token()) {
            return App::$message->get('error', [], 'CSRF Error');
        }
        if (strlen($input['author'])<3) {
            return App::$message->get('form_error_name');
        }
        if (strlen($input['txt'])<10) {
            return App::$message->get('form_error_msg_too_short');
        }
        if (strlen($input['txt'])>512) {
            return App::$message->get('form_error_msg_too_long');
        }
        if (!preg_match("/^[A-Za-z0-9-_\.]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input['email'])) {
            return App::$message->get('form_error_email');
        }
        if ( !array_key_exists('IMG_CODE', $_SESSION) || $input['img_code'] != $_SESSION['IMG_CODE']) {
            return App::$message->get('form_error_code');
        } 
        return true;        
    }
    
    private function requestDone(array $input) : void 
    {
        $input['ip'] = App::$server['REMOTE_ADDR'];
        $input['date'] = 'now()';
        // $input[txt]=strip_tags($input[txt],"<b><i><p><br>");
        $input['txt'] = $this->editor->GetHTML();
        unset($input['img_code']);
        $query = "insert into {$this->TABLE} " . db_insert_fields($input);
        App::$db->query($query);
        $message='Автор: ' . $input['author'] . PHP_EOL;
        $message.='E-Mail: ' . $input['email'] . PHP_EOL;
        $message.='IP: ' . $input['ip'] . PHP_EOL;
        $message.='Сообщение:' . PHP_EOL;
        $message.=str_replace('\r\n', PHP_EOL, $input['txt']) . PHP_EOL;
        echo $message;
        if(!App::$debug){
            App::$message->mail(App::$settings['email_to_addr'], 'На сайте http://' . App::$server['HTTP_HOST'] . App::$SUBDIR . ' оставлено новое сообщение.', $message);
        }
    }
    
    public function actionAdd() : string 
    {
        global $_SESSION;
        $content = '';
        if (is_array(App::$input['form'])) {
            App::$input['form']['txt'] = $this->editor->GetValue();
            $input_result = $this->checkInput(App::$input['form']);
            if($input_result === true) {
                $this->requestDone(App::$input['form']);
                $_SESSION['IMG_CODE'] = rand(111111, 999999);
                $content = App::$message->get('', [], 'Сообщение успешно добавлено !');
                return $content . $this->actionIndex();                
            } else {
                $content .= $input_result;
                $tags = App::$input['form'];
                $this->editor->SetValue(stripcslashes(App::$input['form']['txt']));
            }            
        } else {
            $tags = [
                'author' => '',
                'email' => '',
                'txt' => '',                
            ];
        }
        $tags['editor'] = $this->editor->GetContol(400, 200, '../images/bbcode_editor');
        $tags['functions'] = ['get_csrf_token'];
        $_SESSION['IMG_CODE'] = rand(111111, 999999);
        $content .= App::$template->parse('faq_form', $tags);
        return $content;
    }
    
}
