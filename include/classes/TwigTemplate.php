<?php

namespace classes;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Stormiix\Twig\Extension\MixExtension;

use classes\App;

class TwigTemplate 
{
    private $twig;
    
    const TYPE_STRING = 0;
    const TYPE_ARRAY = 1;
    const TYPE_FILE = 2;

    private $template_type;

    private $config = [
        'debug' => false,
        'template_dirs' => [
            'templates',
            'admin/templates',
        ],
        'cache_dir' => 'var/cache/twig',
        'autoescape' => '',
        'extensions' => [
        ],
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
    public function __construct(int $template_type, array $config_override = [], string $content = null)
    {
        $this->template_type = $template_type;
        
        if(is_array($config_override)) {
            foreach($config_override as $key => $value) {
                $this->config[$key] = $value;
            }
        }
        
        switch ($this->template_type) {
            case $this::TYPE_FILE:
                    $loader = new FilesystemLoader();
                    foreach($this->config['template_dirs'] as $path) {
                        $loader->addPath(App::$DIR . $path);
                    }
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
                App::$message->error('Unknown template type');
                return null;
        }
        
        $environment = new Environment($loader, [
            'cache' => $this->config['debug'] ? false : App::$DIR . $this->config['cache_dir'],
            'debug' => $this->config['debug'],
            'strict_variables' => $this->config['debug'],
            'auto_reload' => $this->config['debug'],
            'autoescape' => $this->config['autoescape'],
        ]);

        if ($this->config['debug']) {
            $environment->addExtension(new DebugExtension());
        }
      
        $mix = new MixExtension(
            App::$DIR . 'theme/',     // the absolute public directory
            'mix-manifest.json'   // the manifest filename (default value is 'mix-manifest.json')
        );
        $environment->addExtension($mix);
        
        $environment->addGlobal('SUBDIR', App::$SUBDIR);
        $environment->addGlobal('PHP_SELF', App::$server['PHP_SELF']);
        $environment->addGlobal('PHP_SELF_DIR', App::$server['PHP_SELF_DIR']);
        $environment->addGlobal('settings', App::$settings);
        $environment->addGlobal('server', App::$server);
        $environment->addGlobal('routing', App::$routing);

        $this->twig = $environment;
    }

    /**
     * Add custom function
     *
     * @param string $name Function name
     *
     * @return null
     */
    public function addFunction(string $name)
    {
        $this->twig->registerUndefinedFunctionCallback(function ($name) {
            if (function_exists($name)) {
                return new TwigFunction($name, $name);
            }
            return false;
        });    
    }

    /**
     * Add path for search
     *
     * @param string $path Function name
     *
     * @return null
     */
    public function addPath(string $path)
    {
        $loader = $this->twig->getLoader();
        $loader->addPath(App::$DIR . $path);
    }

    /**
     * Get path for search
     *
     * @return array
     */
    public function getPaths()
    {
        $loader = $this->twig->getLoader();
        return $loader->getPaths();
    }
    /**
     * Create template from string
     *
     * @param string $template Template content
     *
     * @return $template
    public function create_template($template)
    {
        return $this->twig->createTemplate($template);
    }        
     */

    /**
     * Render twig object
     *
     * @param string $name Template name
     * @param array $params 
     *
     * @return null
     */
    public function render(string $name, array $params = []) : string
    {
        if ($this->template_type === $this::TYPE_STRING) {
            $name = 'template';
        }
        if ($this->template_type === $this::TYPE_FILE && !strstr($name,'.html.twig') ) {
            $name .= '.html.twig';
        }
        return $this->twig->render($name, $params);
    }

}