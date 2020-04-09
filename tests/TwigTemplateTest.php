<?php

use PHPUnit\Framework\TestCase;
use Classes\TwigTemplate;

class TwigTemplateTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testStringParse()
    {
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, [], 'Hello {{ name }}');
        $content = $twig->render('', ['name'=>'Test']);
        self::assertEquals('Hello Test', $content);
    }
    
    public function testFunctionParse()
    {
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, [], '{{ TwigTest(123) }}');
        function TwigTest($param){
            return 'TwigTestFunction: ' . $param;
        }
        $twig->addFunction('TwigTest');
        $content = $twig->render('', []);
        self::assertEquals('TwigTestFunction: 123', $content);
    }
    
    public function testSQLParse()            
    {
        $result=Classes\App::$db->query("select login from users where login='boot'");
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, [], '{% for row in rows %}{{ row.login }}{% endfor %}');
        $content = $twig->render('index.html.twig', ['name'=>'Test', 'rows' => $rows]);
        self::assertEquals('boot', $content);
    }
    
}

