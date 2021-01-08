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
        $this->tags['isIndexPage'] = true;        
        $this->breadcrumbs = [];
        if(class_exists('\local\IndexController')) {
            $controller = new \local\IndexController();
            $content = $controller->actionIndex();
            $this->title = $controller->title;
            $this->tags = $controller->tags;
            return $content;
        }
        
        $query="select title,content from article_item where seo_alias='main'";
        $result=App::$db->query($query);
        list($title,$text)=$result->fetch_array();
        
        $this->title = $title;
        $this->tags['isIndexPage'] = true;
        $content = \replace_base_href($text);
        return $content;
        
    }    
}
