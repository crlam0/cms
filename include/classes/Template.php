<?php

namespace classes;

use classes\App;
use classes\MyTemplate;
use classes\TwigTemplate;

class Template 
{
    private $MyTemplate;
    private $TwigTemplate;
    
    public function __construct () 
    {
        $this->MyTemplate = new MyTemplate();
        $config['debug'] = App::$debug;
        if(isset(App::$settings['twig']) && is_array(App::$settings['twig'])) {
            $config = array_merge($config, App::$settings['twig']);
        }
        $this->TwigTemplate = new TwigTemplate(TwigTemplate::TYPE_FILE, $config);
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
            foreach ($this->TwigTemplate->getPaths() as $path) {
                if (file_exists($path . '/' . $template['file_name'])) {
                    $file_name = $template['file_name'];
                }
            }            
            if ($file_name) {
                $twig = $this->TwigTemplate;
                $template['name'] = $file_name;
            } else {
                $tags['file_name'] = $template['file_name'];
                return App::$message->get('file_not_found', $tags);
            }
        } else {
            $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, ['debug' => App::$debug], $template['content']);
        }        

        if($sql_result instanceof \mysqli_result) {
            $tags['rows'] = $sql_result->fetch_all(MYSQLI_ASSOC);
        }
        if(strlen($inner_content)) {
            $tags['inner_content'] = $inner_content;        
        } else {
            $tags['inner_content'] = '';
        }
        
        $twig->addFunction('get_block');
        $twig->addFunction('widget');
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
     * Add path for search Twig templates.
     *
     * @param string $path Function name
     *
     * @return void
     */
    public function addPath(string $path): void
    {
        $loader = $this->TwigTemplate->addPath($path);
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
        return $this->MyTemplate->parse($template['content'], $tags, $sql_result, $inner_content);
    }

    /**
     * Parse template by name
     *
     * @param string $name Template's name
     * @param array $tags Tags array
     * @param mysqli_result $sql_result Result from SQL query
     * @param string $inner_content Inner content
     *
     * @return string Output content
     */
    function parse(string $name, array $tags = [], ?\mysqli_result $sql_result = null, string $inner_content = '') : string 
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
        if (strpos(App::$server['REQUEST_URI'], 'admin/') && file_exists(dirname(App::$server['SCRIPT_FILENAME']) . '/admin/templates.tpl')) {
            $temp = $this->MyTemplate->loadFromFile(dirname(App::$server['SCRIPT_FILENAME']) . '/admin/templates.tpl', $name);
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
            $template = App::$db->getRow("SELECT * FROM templates WHERE name='{$name}' AND '" . App::$server['REQUEST_URI'] . "' LIKE concat('%',uri,'%')");
        }
        if (!$template) {
            $template = App::$db->getRow("SELECT * FROM templates WHERE name='{$name}'");
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