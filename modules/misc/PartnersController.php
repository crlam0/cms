<?php

namespace modules\misc;

use Classes\BaseController;
use Classes\App;

class PartnersController extends BaseController
{    
    public function actionIndex()
    {
        $this->title = 'Наши партнеры';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from partners order by pos asc";
        $result = App::$db->query($query);
        return App::$template->parse('partners_list_table', [], $result);        
    }
}

