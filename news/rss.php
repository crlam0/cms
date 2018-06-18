<?php
$tags[Header]='Новости из RSS';
include '../include/common.php';

$tags['nav_str'].="<span class=nav_next>{$tags['Header']}</span>";

$xmlstr = @file_get_contents($settings[rss_url]);
if ( $xmlstr===false ){
	$content=my_msg_to_str("rss_error");
	echo get_tpl_by_title($part['tpl_name'],$tags,'',$content);
	exit;
}

$xml = xml_parser_create();
xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
xml_parse_into_struct($xml, $xmlstr, $element, $index);
xml_parser_free($xml);

$count = count($index['TITLE'])-1;

print_debug("RSS count: $count");

$content='';
$count=$settings['rss_count'];

for ($i=0; $i < $count; $i++) {
        //echo '<h1>'.$element[$index["TITLE"][$i+1]]["value"].'</h1>';
        //echo $element[$index["DESCRIPTION"][$i+1]]["value"];
	$tags['title']=$element[$index['TITLE'][$i+1]]['value'];
	$tags['content']=$element[$index['DESCRIPTION'][$i+1]]['value'];
	$tags['content']=str_replace("<img ","<img class=border ",$tags['content']);
	$tags['content'].="<div><a class=button href=\"".$element[$index['LINK'][$i+1]]['value']."\"> Подробнее ... </a></div>";
	$content.=get_tpl_by_title('news_rss', $tags, $result);
}

echo get_tpl_by_title($part['tpl_name'],$tags,'',$content);
