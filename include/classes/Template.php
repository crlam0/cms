<?php

namespace Classes;


use Classes\MyTemplate;
use Classes\TwigTemplate;
use Classes\App;

class Template 
{
    private $MyTemplate;
    private $TwigTemplate;
    
    public function __construct () 
    {
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
    private function parseTwigTemplate(array $template, array $tags = [], $sql_result = [], string $inner_content = '') : string
    {        
        if ($template['file_name']) {        
            if(!strstr($template['file_name'],'.html.twig')) {
                $template['file_name'].='.html.twig';
            }
            $file_name = '';
            if (file_exists(App::$DIR . 'templates/' . $template['file_name'])) {
                $file_name = App::$DIR . 'templates/' . $template['file_name'];
                $file_name = $template['file_name'];
            }    
            if ($file_name) {
                $twig = $this->TwigTemplate;
                $template['name'] = $file_name;
            } else {
                $tags['file_name'] = $template['file_name'];
                return App::$message->get('file_not_found', $tags);
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
        
        $twig->addFunction('add_block');
        $twig->addFunction('include_php');
        $twig->addFunction('path');
        if(array_key_exists('functions',$tags) && is_array($tags['functions'])) {
            foreach($tags['functions'] as $function) {
                $twig->addFunction($function);
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
    private function parseMyTemplate(array $template, array $tags = [], $sql_result = [], string $inner_content = '') : string
    {
        if (array_key_exists('file_name', $template) && $template['file_name']) {
            $file_name = '';
            if (file_exists($template['file_name'])) {
                $file_name = $template['file_name'];
            }    
            if (file_exists(App::$DIR . $template['file_name'])) {
                $file_name = App::$DIR . $template['file_name'];
            }    
            if ($file_name) {
                $template['content'] = implode('', file($file_name));
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
    function parse(string $name, array $tags = [], $sql_result = [], string $inner_content = '') : string 
    {
                
        $template = null;

        if (file_exists(dirname(App::$server['SCRIPT_FILENAME']) . '/templates.tpl')) {
            $temp = $this->MyTemplate->loadFromFile(dirname(App::$server['SCRIPT_FILENAME']) . '/templates.tpl', $name);
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
        App::debug("Parse template '{$template['name']}'");    
        if ($template['template_type'] === 'my') {
            return $this->parseMyTemplate($template, $tags, $sql_result, $inner_content);        
        } elseif($template['template_type'] === 'twig') {
            return $this->parseTwigTemplate($template, $tags, $sql_result, $inner_content);
        } else {
            $tags['type'] = $template['template_type'];
            App::$message->get('', $tags, 'Unknown template type "[%type%]"');
            return '';
        }
    }

}