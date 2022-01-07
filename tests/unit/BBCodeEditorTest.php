<?php

use classes\BBCodeEditor;

class BBCodeEditorTest extends \Codeception\Test\Unit
{

    public function testBBCodeEditor(): void
    {
        $BBCodeEditor = new BBCodeEditor();
        $content = $BBCodeEditor->GetContol(100, 100, '');
        self::assertStringStartsWith("        <style>", $content);
    }
}
