<?php

use PHPUnit\Framework\TestCase;
use Classes\BBCodeEditor;

require_once 'tests/require.php';

class BBCodeEditorTest extends TestCase
{

    public function testBBCodeEditor()
    {
        $BBCodeEditor = new BBCodeEditor();
        $content = $BBCodeEditor->GetContol(100,100,'');
        self::assertStringStartsWith("        <style>", $content);
    }
    
}

