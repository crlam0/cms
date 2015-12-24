<?php

include $INC_DIR . "lib_bbcode.php";

class COMMENTS
{
    private $__target_type;
    private $__target_id;
    private $__editor;
    private $__code_ok;
    private $__new_form;
    private $__table = "comments";
    private $__get_form_data_result = "";
    
    function __construct($target_type,$target_id = 0,$action_href = ""){
        $this->__target_type=$target_type;
        $this->__target_id=$target_id;
        $this->__editor = new BBCODE_EDITOR ();
        $this->__new_form = true;
    }

    function show_count($target_id) {
        global $conn;
        $query="select count(id) from {$this->__table} where active='Y' and target_type='{$this->__target_type}' and target_id={$target_id}";
        list($count) = my_select_row($query, true);
        return $count;
    }
    
    function show_list($tags = array ()) {
        global $conn;
        $query="select * from {$this->__table} where active='Y' and target_type='{$this->__target_type}' and target_id={$this->__target_id} order by id asc";
        $result=  my_query($query, $conn);
        return get_tpl_by_title("comments_list",$tags,$result);        
    }
    
    function show_form($tags = array ()) {
        global $_SESSION,$SUBDIR,$editor,$input;
        if ( $this->__new_form ) {
            $this->__editor->SetValue("");
        }elseif (is_array($input[form])) {
            $data = $input[form];
            $tags = array_merge($tags, $data);            
        }
        $tags[editor] = $this->__editor->GetContol(400, 200, $SUBDIR . "images/bbcode_editor");
        if(!strlen($tags["action"])) $tags["action"] = $_SERVER["PHP_SELF"];        
        $_SESSION["IMG_CODE"] = rand(111111, 999999);        
        return $this->__get_form_data_result.get_tpl_by_title("comment_add_form", $tags);
    }

    function get_form_data($input){
        global $_SERVER,$SUBDIR,$settings;
        if ($input["add_comment"]) { 
            $err = 0;            
            if (strlen($input[form]["author"]) < 3) {
                $output.=my_msg_to_str("form_error_name");
                $err = 1;
            } elseif (!preg_match("/^[A-Za-z0-9-_]+@[A-Za-z0-9-\.]+\.[A-Za-z0-9-\.]{2,3}$/", $input[form]["email"])) {
                $output.=my_msg_to_str("form_error_email");
                $err = 1;
            } elseif (strlen($this->__editor->GetValue()) < 10) {
                $output.=my_msg_to_str("form_error_msg_too_short");
                $err = 1;
            } elseif ( ($input["img_code"] != $_SESSION["IMG_CODE"]) && (!$settings["debug"]) ) {
                $output.=my_msg_to_str("form_error_code");
                $err = 1;
            }
            if ( $err ) {
                $this->__new_form = false;
            } else {
                $input[form][ip] = $_SERVER["REMOTE_ADDR"];
                $input[form][date_add] = "now()";
                $input[form][uid] = $_SESSION["UID"];
                $input[form][target_type]=$this->__target_type;
                $input[form][target_id]=$this->__target_id;
                $input[form][content]=$this->__editor->GetHTML();
                $query = "insert into {$this->__table} " . db_insert_fields($input[form]);
                $result = my_query($query, $conn);
                $output.=my_msg_to_str("","","Комментарий успешно добавлен");
                $msg="Автор: {$input[form][author]} ( {$input[form][ip]} )\n";
                $msg.="Сообщение:\n";
                $msg.=$content."\n";
                if(!$settings["debug"])send_mail($settings["email_to_addr"], "На сайте http://".$_SERVER["HTTP_HOST"].$SUBDIR." оставлен новый комментарий.", $msg);
                $this->__new_form = true;
            }
            $this->__get_form_data_result = $output;
        }
    }
}

?>
