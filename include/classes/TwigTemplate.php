<?php

namespace Classes;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TwigTemplate {
    private $twig;
    
    const TYPE_STRING = 0;
    const TYPE_ARRAY = 1;
    const TYPE_FILE = 2;

    private $template_type;

    private $config = [
        'debug' => false,
        'template_dir' => 'templates',
        'cache_dir' => 'var/cache/twig',
        'autoescape' => 'name',
        'extensions' => [],
    ];

    /**
     * Create twig object
     *
     * @param string $template_type Template type
     * @param array $config_override Override config parameters
     * @param array $content Template content for  string and array types
     *
     * @return null
     */
    public function __construct($template_type, $config_override = [], $content = null)
    {
        global $DIR;
        $this->template_type = $template_type;
        
        if(is_array($config_override)) {
            foreach($config_override as $key => $value) {
                $this->config[$key] = $value;
            }
        }
        
        switch ($this->template_type) {
            case $this::TYPE_FILE:
                    $loader = new FilesystemLoader();
                    $loader->addPath($DIR . $this->config['template_dir']);
                break;
            case $this::TYPE_ARRAY:
                    $loader = new ArrayLoader($content);
                break;
            case $this::TYPE_STRING:
                    $loader = new ArrayLoader([
                        'template' => $content,
                    ]);
                break;
            default:
                print_error('Unknown template type');
                return null;
        }
        
        $environment = new Environment($loader, [
            'cache' => $this->config['debug'] ? false : $DIR . $this->config['cache_dir'],
            'debug' => $this->config['debug'],
            'strict_variables' => $this->config['debug'],
            'auto_reload' => $this->config['debug'],
            'autoescape' => $this->config['autoescape'],
        ]);

        if ($this->config['debug']) {
            $environment->addExtension(new DebugExtension());
        }

        $this->twig = $environment;
    }

    /**
     * Create twig object
     *
     * @param string $template_type Template type
     * @param array $config_override Override config parameters
     * @param array $content Template content for  string and array types
     *
     * @return null
     */
    public function AddFunction($name)
    {
        $this->twig->registerUndefinedFunctionCallback(function ($name) {
            if (function_exists($name)) {
                return new TwigFunction($name, $name);
            }
            return false;
        });    
    }
    
    /**
     * Create template from string
     *
     * @param string $template_type Template type
     *
     * @return $template
     */
    public function createTemplate($template)
    {
        return $this->twig->createTemplate($template);
    }        

    /**
     * Render twig object
     *
     * @param string $name Template name
     * @param array $params 
     *
     * @return null
     */
    public function render($name, array $params = [])
    {
        if ($this->template_type === $this::TYPE_STRING) {
            $name='template';
        }
        if ($this->template_type === $this::TYPE_FILE && !strstr($name,'.html.twig') ) {
            $name.='.html.twig';
        }
        return $this->twig->render($name, $params);
    }

}