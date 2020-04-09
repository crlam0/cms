<?php
$tags['Header']='Страница администрирования';
require '../include/common.php';

use Classes\Sitemap;

$content='';


if($input['clear_cache']) {
    if(clear_cache_dir()){
        $content.=my_msg_to_str('','','Директория кэша очищена');
    } else {
        $content.=my_msg_to_str('error','','Директория кэша не очищена !');
    }
}

$sitemap=$DIR . 'sitemap.xml';

if(file_exists($sitemap)){
    $time_diff=time()-filemtime($sitemap);
}

if($time_diff>7*24*60*60){
    $content.=my_msg_to_str('','','Файл sitemap.xml не обновлялся более недели.');
    $sitemap= new Sitemap();
    $types = explode(';', $settings['sitemap_types']);
    $sitemap->build_pages_array($types);
    $result=$sitemap->write();    
    $content.=my_msg_to_str('','',"Файл sitemap.xml сгенерирован, записано {$result['count']} позиций.");
}

$tables = my_query("SHOW TABLES LIKE 'comments'");
if($tables->num_rows){
    $query="select * from comments order by date_add desc limit 5";
    $result=my_query($query);
    if($result->num_rows){
        $content.=get_tpl_by_name('admin_last_comments',$tags,$result);
    }    
}

$tables = my_query("SHOW TABLES LIKE 'request'");
if($tables->num_rows){
    $query="select * from request order by date desc limit 5";
    $result=my_query($query);
    if($result->num_rows){
        $content.=get_tpl_by_name('admin_last_requests',$tags,$result);
    }    
}

$query="SELECT day,count(id) as hits,sum(unique_visitor) as unique_hits from visitor_log group by day order by day desc limit 12";
$result=my_query($query);
$content.=get_tpl_by_name("stats_day_table",$tags,$result);

$query="SELECT * from visitor_log where unique_visitor=1 order by id desc limit 20";
$result=my_query($query);
$content.=get_tpl_by_name('stats_last_visitors_table',$tags,$result);

echo get_tpl_by_name($part['tpl_name'],$tags,'',$content);
