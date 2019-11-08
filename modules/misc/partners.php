<?php
if(!isset($input)) {
    require '../../include/common.php';
}
$tags['Header'] = 'Наши партнеры';

$query = "select * from partners order by pos asc";
$result = my_query($query, true);
$content = get_tpl_by_name('partners_list_table', $tags, $result);

echo get_tpl_by_name($part[tpl_name], $tags, '', $content);