<?php

namespace Classes;


use Classes\MyTemplate;
use Classes\TwigTemplate;
use Classes\App;

class Template {
    private $MyTemplate;
    private $TwigTemplate;
    
    public function __construct () {
        $this->MyTemplate = new MyTemplate();
        $this->TwigTemplate = new TwigTemplate(TwigTemplate::TYPE_FILE, ['debug' => App::$settings['debug']]);
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
    private function parse_twig_tpl($template, $tags = [], $sql_result = [], $inner_content = ''){        
        App::debug("Parse template '{$template['name']}'");    
        if ($template['file_name']) {        
            if(!strstr($template['file_name'],'.html.twig')) {
                $template['file_name'].='.html.twig';
            }
            $fname = '';
            if (file_exists(App::$DIR . 'templates/' . $template['file_name'])) {
                $fname = App::$DIR . 'templates/' . $template['file_name'];
                $fname = $template['file_name'];
            }    
            if ($fname) {
                $twig = $this->TwigTemplate;
                $template['name'] = $fname;
            } else {
                $tags['file_name'] = $template['file_name'];
                App::$message->get('file_not_found', $tags);
                return '';
            }
        } else {
            $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, ['debug' => App::$settings['debug']], $template['content']);
        }

        if($sql_result instanceof \mysqli_result) {
            $tags['rows'] = $sql_result->fetch_all(MYSQLI_ASSOC);
        }
        if(strlen($inner_content)) {
            $tags['inner_content'] = $inner_content;        
        } else {
            $tags['inner_content'] = '';
        }
        
        $twig->add_function('add_block');
        $twig->add_function('include_php');
        $twig->add_function('path');
        if(array_key_exists('functions',$tags) && is_array($tags['functions'])) {
            foreach($tags['functions'] as $function) {
                $twig->add_function($function);
            }
            unset($tags['functions']);
        }        
        return $twig->render($template['name'], $tags);
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
    private function parse_my_tpl($template, $tags = [], $sql_result = [], $inner_content = ''){
        if (array_key_exists('file_name', $template) && $template['file_name']) {
            $fname = '';
            if (file_exists($template['file_name'])) {
                $fname = $template['file_name'];
            }    
            if (file_exists(App::$DIR . $template['file_name'])) {
                $fname = App::$DIR . $template['file_name'];
            }    
            if ($fname) {
                $template['content'] = implode('', file($fname));
            } else {
                $tags['file_name'] = $template['file_name'];
                App::$message->get('file_not_found', $tags);
                return '';
            }
        }
        if (!strstr($template['content'], '[%')) {
            return($template['content']);
        }
        App::debug("Parse template '{$template['name']}'");
        return $this->MyTemplate->parse($template['content'], $tags, $sql_result, $inner_content);
    }

    /**
     * Parse template by name
     *
     * @param string $name Template's name
     * @param array $tags Tags array
     * @param array $sql_result Result from SQL query
     * @param string $inner_content Inner content
     *
     * @return string Output content
     */
    function parse($name, $tags = [], $sql_result = [], $inner_content = '') {
                
        $template = null;

        if (file_exists(dirname(App::$server['SCRIPT_FILENAME']) . '/templates.tpl')) {
            $temp = $this->MyTemplate->load_from_file(dirname(App::$server['SCRIPT_FILENAME']) . '/templates.tpl', $name);
            if ($temp) {
                $template['name'] = $name;            
                $template['content'] = $temp;
                $template['template_type'] = 'my';
            }
        }
        if(strstr($name,'.tpl')) {
            $template['name'] = $name;
            $template['file_name']=$name;
            $template['template_type'] = 'my';
        }
        if(strstr($name,'.html.twig')) {
            $template['name'] = $name;
            $template['file_name']=$name;
            $template['template_type'] = 'twig';
        }
        if (!$template) {
            $template = App::$db->select_row("SELECT * FROM templates WHERE name='{$name}' AND '" . App::$server['REQUEST_URI'] . "' LIKE concat('%',uri,'%')", true);
        }
        if (!$template) {
            $template = App::$db->select_row("SELECT * FROM templates WHERE name='{$name}'", true);
        }
        if (!$template) {
            return App::$message->get('tpl_not_found', ['name'=>$name]);
        }
        if ($template['template_type'] === 'my') {
            return $this->parse_my_tpl($template, $tags, $sql_result, $inner_content);        
        } elseif($template['template_type'] === 'twig') {
            return $this->parse_twig_tpl($template, $tags, $sql_result, $inner_content);
        } else {
            $tags['type'] = $template['template_type'];
            App::$message->get('', $tags, 'Unknown template type "[%type%]"');
            return '';
        }
    }

}