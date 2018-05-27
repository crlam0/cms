<?php
$tags['Header']="Статистика посещений";
include "../include/common.php";
$query="SELECT day,count(id) as hits,sum(unique_visitor) as unique_hits from visitor_log group by day order by day desc limit 31";
$result=my_query($query);
$content.=get_tpl_by_title("stats_day_table",$tags,$result);

$query="SELECT remote_host,count(id) as hits from visitor_log group by remote_host order by hits desc limit 20";
$result=my_query($query);
$content.=get_tpl_by_title("stats_hosts_table",$tags,$result);

$query="SELECT remote_addr,count(id) as hits from visitor_log group by remote_addr order by hits desc limit 20";
$result=my_query($query);
$content.=get_tpl_by_title("stats_addr_table",$tags,$result);

$query="SELECT user_agent,count(id) as hits from visitor_log group by user_agent order by hits desc limit 10";
$result=my_query($query);
$content.=get_tpl_by_title("stats_user_agent_table",$tags,$result);

$query="SELECT script_name,count(id) as hits from visitor_log group by script_name order by hits desc";
$result=my_query($query);
$content.=get_tpl_by_title("stats_script_name_table",$tags,$result);

echo get_tpl_by_title($part['tpl_name'],$tags,"",$content);	
?>