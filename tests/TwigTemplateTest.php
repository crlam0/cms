<?php

use PHPUnit\Framework\TestCase;

require_once 'tests/require.php';
  
use Classes\Blocks;
use Classes\TwigTemplate;

class TwigTemplateTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testStringParse()
    {
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, true, 'Hello {{ name }}');
        $content = $twig->render('', ['name'=>'Test']);
        self::assertEquals('Hello Test', $content);
    }
    
    public function testFunctionParse()
    {
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, true, '{{ TwigTest(123) }}');
        function TwigTest($param){
            return 'TwigTestFunction: ' . $param;
        }
        $twig->AddFunction('TwigTest');
        $content = $twig->render('', []);
        self::assertEquals('TwigTestFunction: 123', $content);
    }
    
    public function testSQLParse()            
    {
        $result=my_query('select login from users where id=7');
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $twig = new TwigTemplate(TwigTemplate::TYPE_STRING, true, '{% for row in rows %}{{ row.login }}{% endfor %}');
        $content = $twig->render('index.html.twig', ['name'=>'Test', 'rows' => $rows]);
        self::assertEquals('boot', $content);
    }
    
}

