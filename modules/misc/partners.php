<?php
$tags['Header'] = 'Наши партнеры';

$query = "select * from partners order by pos asc";
$result = my_query($query, true);
$content = get_tpl_by_title('partners_list_table', $tags, $result);

echo get_tpl_by_title($part[tpl_name], $tags, '', $content);