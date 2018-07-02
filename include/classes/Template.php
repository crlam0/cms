<?php

namespace Classes;

use Classes\Blocks;
use Classes\BlocksLocal;
use Classes\MyTemplate;
use Classes\TwigTemplate;

class Template {
    private $BlocksObject;
    private $MyTemplate;
    
    public function __construct () {
        global $INC_DIR;
        if(file_exists($INC_DIR.'classes/BlocksLocal.php')) {
            $this->BlocksObject = new BlocksLocal();    
        } else {
            $this->BlocksObject = new Blocks();
        }
        $this->MyTemplate = new MyTemplate($this->BlocksObject);
    }

    /**
     * Parse Twig template by array
     *
     * @param array $template Template
     * @param array $tags Tags array
     * @param array $sql_result Result from SQL query
     * @param string $inner_content Inner content
     *
     * @return string Output content
     */
    private function get_twig_tpl($template, $tags = [], $sql_result = [], $inner_content = ''){
        global $DIR, $settings;
        if ($template['file_name']) {        
            if(!strstr($template['file_name'],'.html.twig')) {
                $template['file_name'].='.html.twig';
            }
            $fname = '';
            if (file_exists($DIR . 'templates/' . $template['file_name'])) {
                $fname = $DIR . 'templates/' . $template['file_name'];
                $fname = $template['file_name'];
            }    
            if ($fname) {
                $twig = new TwigTemplate(TwigTemplate::TYPE_FILE, ['debug' => $settings['debug']]);
            } else {
                $tags['file_name'] = $template['file_name'];
                my_msg('file_not_found', $tags);
                return '';
            }
        } else {
            $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, ['debug' => $settings['debug']], $template['content']);
        }
        add_to_debug("Parse template '{$template['title']}'");    

        if($sql_result instanceof \mysqli_result) {
            $tags['rows'] = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
        }
        if(strlen($inner_content)) {
            $tags['inner_content'] = $inner_content;        
        }
        if(is_array($tags['functions'])) {
            foreach($tags['functions'] as $function) {
                $twig->AddFunction($function);
            }        
        }
        return $twig->render($template['title'], $tags);
    }

    /**
     * Parse my template by array
     *
     * @param array $template Template
     * @param array $tags Tags array
     * @param array $sql_result Result from SQL query
     * @param string $inner_content Inner content
     *
     * @return string Output content
     */
    private function get_my_tpl($template, $tags = [], $sql_result = [], $inner_content = ''){
        global $DIR;
        if (array_key_exists('file_name', $template) && $template['file_name']) {
            $fname = '';
            if (file_exists($template['file_name'])) {
                $fname = $template['file_name'];
            }    
            if (file_exists($DIR . $template['file_name'])) {
                $fname = $DIR . $template['file_name'];
            }    
            if ($fname) {
                $template['content'] = implode('', file($fname));
            } else {
                $tags['file_name'] = $template['file_name'];
                my_msg('file_not_found', $tags);
                return '';
            }
        }
        if (!strstr($template['content'], '[%')) {
            return($template['content']);
        }
        add_to_debug("Parse template '{$template['title']}'");
        return $this->MyTemplate->parse($template['content'], $tags, $sql_result, $inner_content);
    }


    /**
     * Parse template by title
     *
     * @param string $title Template's title
     * @param array $tags Tags array
     * @param array $sql_result Result from SQL query
     * @param string $inner_content Inner content
     *
     * @return string Output content
     */
    function get_tpl_by_title($title, $tags = [], $sql_result = [], $inner_content = '') {
        global $server, $DIR;
        
        $template = null;

        if (file_exists(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl')) {
            $temp = $this->MyTemplate->load_from_file(dirname($server['SCRIPT_FILENAME']) . '/templates.tpl', $title);
            if ($temp) {
                $template['title'] = $title;            
                $template['content'] = $temp;
                $template['template_type'] = 'my';
            }
        }
        if(strstr($title,'.tpl')) {
            $template['title'] = $title;
            $template['file_name']=$title;
            $template['template_type'] = 'my';
        }
        if(strstr($title,'.html.twig')) {
            $template['title'] = $title;
            $template['file_name']=$title;
            $template['template_type'] = 'twig';
        }
        if (!$template) {
            $template = my_select_row("SELECT * FROM templates WHERE title='$title' AND '" . $server["REQUEST_URI"] . "' LIKE concat('%',uri,'%')", true);
        }
        if (!$template) {
            $template = my_select_row("SELECT * FROM templates WHERE title='$title'", true);
        }
        if (!$template) {
            $tags['title'] = $title;
            my_msg('tpl_not_found', $tags);
            return '';
        }
        if ($template['template_type'] === 'my') {
            return $this->get_my_tpl($template, $tags, $sql_result, $inner_content);        
        } elseif($template['template_type'] === 'twig') {
            return $this->get_twig_tpl($template, $tags, $sql_result, $inner_content);
        } else {
            $tags['type'] = $template['template_type'];
            my_msg('', $tags, 'Unknown template type "[%type%]"');
            return '';
        }
    }

}