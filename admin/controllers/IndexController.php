<?php

namespace admin\controllers;

use classes\App;
use classes\BaseController;
use classes\Sitemap;

class IndexController extends BaseController
{
    private $sitemap;

    public function __construct()
    {
        parent::__construct();
        $this->sitemap = App::$DIR . 'sitemap.xml';
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $this->title = 'Административный раздел';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $content = '';

        $time_diff = 0;
        
        if (file_exists($this->sitemap)) {
            $time_diff=time()-filemtime($this->sitemap);
        }

        if ($time_diff>7*24*60*60) {
            $content .= App::$message->get('', [], 'Файл sitemap.xml не обновлялся более недели.');
            $sitemap = new Sitemap();
            $types = explode(';', App::$settings['sitemap_types']);
            $sitemap->build_pages_array($types);
            $result = $sitemap->write();
            $content .= App::$message->get('', [], "Файл sitemap.xml сгенерирован, записано {$result['count']} позиций.");
        }
        $tables = App::$db->query("SHOW TABLES LIKE 'comments'");
        if ($tables->num_rows) {
            $query = "select * from comments order by date_add desc limit 5";
            $result = App::$db->query($query);
            if ($result->num_rows) {
                $content .= App::$template->parse('admin_last_comments.html.twig', [], $result);
            }
        }

        $tables = App::$db->query("SHOW TABLES LIKE 'request'");
        if ($tables->num_rows) {
            $query = "select * from request order by date desc limit 5";
            $result = App::$db->query($query);
            if ($result->num_rows) {
                $content .= App::$template->parse('admin_last_requests.html.twig', [], $result);
            }
        }

        $query = "SELECT day,count(id) as hits,sum(unique_visitor) as unique_hits from visitor_log group by day order by day desc limit 12";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_day_table.html.twig', [], $result);

        $query = "SELECT * from visitor_log where unique_visitor=1 order by id desc limit 20";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_last_visitors_table.html.twig', [], $result);

        return $content;
    }

    public function actionCacheClear(): string
    {
        if (clear_cache_dir('misc') && clear_cache_dir('twig')) {
            return App::$message->get('success', [], 'Директория кэша очищена');
        } else {
            return App::$message->getError('Директория кэша не очищена !');
        }
    }

    public function actionViewStats(): string
    {
        $query="SELECT day,count(id) as hits,sum(unique_visitor) as unique_hits from visitor_log group by day order by day desc limit 31";
        $result = App::$db->query($query);
        $content = App::$template->parse('stats_day_table.html.twig', [], $result);

        $query="SELECT remote_host,count(id) as hits from visitor_log group by remote_host order by hits desc limit 20";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_hosts_table.html.twig', [], $result);

        $query="SELECT remote_addr,count(id) as hits from visitor_log group by remote_addr order by hits desc limit 20";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_addr_table.html.twig', [], $result);

        $query="SELECT user_agent,count(id) as hits from visitor_log group by user_agent order by hits desc limit 10";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_user_agent_table.html.twig', [], $result);

        $query="SELECT request_uri,count(id) as hits from visitor_log group by request_uri order by hits desc limit 20";
        $result = App::$db->query($query);
        $content .= App::$template->parse('stats_script_name_table.html.twig', [], $result);

        return $content;
    }

    public function actionGenSitemap(): string
    {

        $sitemap = new Sitemap();
        $types = explode(';', App::$settings['sitemap_types']);
        $sitemap->build_pages_array($types);

        $result = $sitemap->write();
        $content = $result['output'];

        return App::$message->get('success', [], 'Файл SITEMAP создан, записано ' . $result['count'] . ' позиций') . $content;
    }
}
