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
    private $target_type;
    private $target_id;
    private $editor;
    private $code_ok;
    private $new_form;
    private $table = 'comments';
    private $get_form_data_result = '';

    /**
     * Set object params
     *
     * @param string $target_type
     * @param integer $target_id Target ID
     * @param string $action_href Action HREF
     */
    public function __construct(string $target_type, int $target_id = 0, string $action_href = '')
    {
        $this->target_type=$target_type;
        $this->target_id=$target_id;
        $this->editor = new BBCodeEditor();
        $this->new_form = true;
    }

    /**
     * Show textarea with BBCode controls.
     *
     * @param integer $target_id ID of content
     *
     * @return string
     */
    public function show_count(int $target_id) : string
    {
        $query="select count(id) from {$this->table} where active='Y' and target_type=? and target_id=?";
        list($count) = App::$db->getRow($query, ['target_type' => $this->target_type, 'target_id' => $target_id]);
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
        $query="select {$this->table}.*, users.fullname, users.avatar from {$this->table}
            left join users on (users.id = uid)
            where active='Y' and target_type='{$this->target_type}' and target_id='{$this->target_id}'
            order by id asc";
        $result = App::$db->query($query);
        return App::$template->parse('comments_list', $tags, $result);
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
        if ($this->new_form) {
            $this->editor->SetValue('');
            $tags['author'] = '';
            $tags['email'] = '';
        } elseif (is_array(App::$input['form'])) {
            $data = App::$input['form'];
            $tags = array_merge($tags, $data);
        }
        $tags['editor'] = $this->editor->GetContol(400, 200, App::$SUBDIR . 'theme/bbcode_editor');
        $tags['authorized'] = (App::$user->id > 0);
        if (!isset($tags['action'])) {
            $tags['action'] = dirname(App::$server['PHP_SELF']);
        }
        $_SESSION['IMG_CODE'] = rand(111111, 999999);
        return $this->get_form_data_result . App::$template->parse('comment_add_form', $tags);
    }

    private function checkInput(array $input) : array
    {
        $err = false;
        $output = '';
        if (!check_csrf_token()) {
            $output.=App::$message->get('error', [], 'CSRF Error');
            $err = true;
        } elseif (!isset($input['author']) || strlen($input['author']) < 3) {
            $output.=App::$message->get('form_error_name');
            $err = true;
        } elseif (!isset($input['email']) || !preg_match('/^[A-Za-z0-9-_]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/', $input['email'])) {
            $output.=App::$message->get('form_error_email');
            $err = true;
        } elseif (strlen($this->editor->GetValue()) < 10) {
            $output.=App::$message->get('form_error_msg_too_short');
            $err = true;
        } elseif (strlen($this->editor->GetValue()) > 512) {
            $output.=App::$message->get('form_error_msg_too_long');
            $err = true;
        } elseif (($input['img_code'] != $_SESSION['IMG_CODE']) && (!App::$settings['debug'])) {
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
     * @return false|null
     */
    public function get_form_data($input)
    {
        if (!isset($input) || !$input['add_comment']) {
            return false;
        };
        list($err, $output) = $this->checkInput($input);
        if ($err && !App::$user->id) {
            $this->new_form = false;
        } else {
            $input['ip'] = App::$server['REMOTE_ADDR'];
            $input['date_add'] = 'now()';
            $input['uid'] = App::$user->id;
            $input['target_type']=$this->target_type;
            $input['target_id']=$this->target_id;
            $input['content']=$this->editor->GetHTML();

            if (App::$user->id) {
                $input['author'] = App::$user->fullname;
                $input['email'] = App::$user->email;
            }

            unset($input['add_comment']);
            unset($input['img_code']);
            $query = "insert into {$this->table} " . db_insert_fields($input);
            App::$db->query($query);
            $output.=App::$message->get('', [], 'Комментарий успешно добавлен');

            $remote_host=(check_key('REMOTE_HOST', App::$server) ? App::$server['REMOTE_HOST'] : gethostbyaddr(App::$server['REMOTE_ADDR']) );
            $message="Автор: {$input['author']} ( {$input['email']} )\n";
            $message.="IP: {$input['ip']} ( {$remote_host} )\n";
            $message.="Сообщение:\n";
            $message.=str_replace('\r\n', "\n", $input['content']) . "\n";
            if (!App::$debug) {
                App::$message->mail(App::$settings['email_to_addr'], 'На сайте http://'.App::$server['HTTP_HOST'].App::$SUBDIR.' оставлен новый комментарий.', $message);
            }

            $this->new_form = true;
        }
        $this->get_form_data_result = $output;
    }
}
