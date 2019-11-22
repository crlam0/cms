<?php

namespace Tests;

use Classes\Controller;
use Classes\App;

class MyTestController extends Controller
{
    
    public function actionIndex($arg1, $arg2)
    {
        $this->title = 'Test';
        $this->breadcrumbs[] = ['title'=>'test','url'=>'test/'];
        return $arg1. '<br />' . $arg2;
    }
}

