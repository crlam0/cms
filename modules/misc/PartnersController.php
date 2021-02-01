<?php

namespace modules\misc;

use classes\BaseController;
use classes\App;

class PartnersController extends BaseController
{

    public function actionIndex(): string
    {
        $this->title = 'Наши партнеры';
        $this->breadcrumbs[] = ['title'=>$this->title];
        $query = "select * from partners where active='Y' order by pos asc";
        $result = App::$db->query($query);
        return App::$template->parse('partners_list', [], $result);
    }
}
