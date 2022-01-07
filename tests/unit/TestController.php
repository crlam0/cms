<?php

namespace tests\unit;

use classes\BaseController;

class TestController extends BaseController
{

    public function actionIndex($arg1, $arg2): string
    {
        $this->title = 'Test';
        $this->breadcrumbs[] = ['title'=>'test','url'=>'test/'];
        return $arg1. '<br />' . $arg2;
    }
}
