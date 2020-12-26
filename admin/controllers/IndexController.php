<?php

namespace admin\Controllers;

use classes\App;
use classes\BaseController;
use classes\Sitemap;

class IndexController extends BaseController
{    
    
    private $sitemap;
    
    public function __construct() {
        parent::__construct();
        $this->sitemap = App::$DIR . 'sitemap.xml';
    }

    public function actionIndex(): string
    {
        $this->title = 'Административный раздел';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $content = '';

        $time_diff = 0;
        
        if(file_exists($this->sitemap)){
            $time_diff=time()-filemtime($this->sitemap);
        }        

        if($time_diff>7*24*60*60){
            $content .= App::$message->get('',[],'Файл sitemap.xml не обновлялся более недели.');
            $sitemap = new Sitemap();
            $types = explode(';', App::$settings['sitemap_types']);
            $sitemap->build_pages_array($types);
            $result = $sitemap->write();    
            $content .= App::$message->get('',[],"Файл sitemap.xml сгенерирован, записано {$result['count']} позиций.");
        }      
        $tables = my_query("SHOW TABLES LIKE 'comments'");
        if($tables->num_rows){
            $query = "select * from comments order by date_add desc limit 5";
            $result = App::$db->query($query);
            if($result->num_rows){
                $content .= App::$template->parse('admin_last_comments', [], $result);
            }    
        }

        $tables = my_query("SHOW TABLES LIKE 'request'");
        if($tables->num_rows){
            $query = "select * from request order by date desc limit 5";
            $result = App::$db->query($query);
            if($result->num_rows){
                $content .= App::$template->parse('admin_last_requests', [], $result);
            }    
        }

        $query = "SELECT day,count(id) as hits,sum(unique_visitor) as unique_hits from visitor_log group by day order by day desc limit 12";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_day_table',[],$result);

        $query = "SELECT * from visitor_log where unique_visitor=1 order by id desc limit 20";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_last_visitors_table', [], $result);
        
        return $content;

    }
    
    public function actionCacheClear(): string
    {
        if(clear_cache_dir('misc') && clear_cache_dir('twig')){
            return App::$message->get('success',[],'Директория кэша очищена');
        } else {
            return App::$message->getError('Директория кэша не очищена !');
        }        
    }    
    
}

