<?php

namespace Modules\misc;

use classes\App;
use classes\BaseController;


/**
 * Default controller for index page.
 *
 * @author BooT
 */
class IndexController extends BaseController 
{    
    public function actionIndex(): string
    {
        if(class_exists('\local\IndexController')) {
            $controller = new \local\IndexController();
            return $controller->actionIndex();
        }
        
        $query="select title,content from article_item where seo_alias='main'";
        $result=App::$db->query($query);
        list($title,$text)=$result->fetch_array();
        
        $this->title = $title;
        $content = \replace_base_href($text);
        return $content;
        
    }    
}
