<?php

namespace Tests;

use Classes\BaseController;

class MyTestController extends BaseController
{
    
    public function actionIndex($arg1, $arg2)
    {
        $this->title = 'Test';
        $this->breadcrumbs[] = ['title'=>'test','url'=>'test/'];
        return $arg1. '<br />' . $arg2;
    }
}

