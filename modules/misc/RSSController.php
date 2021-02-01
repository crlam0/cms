<?php

namespace modules\misc;

use classes\BaseController;
use classes\App;

class RSSController extends BaseController
{

    public function actionIndex(): string
    {
        $this->title = 'Новости из RSS';
        $this->breadcrumbs[] = ['title'=>$this->title];

        $xmlstr = @file_get_contents($settings['rss_url']);
        if ($xmlstr===false) {
            return App::$message->get('rss_error');
        }

        $xml = xml_parser_create();
        xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($xml, $xmlstr, $element, $index);
        xml_parser_free($xml);

        $count = count($index['TITLE'])-1;

        App::debug("RSS count: $count");

        $content='';
        $count=$settings['rss_count'];

        for ($i=0; $i < $count; $i++) {
                //echo '<h1>'.$element[$index["TITLE"][$i+1]]["value"].'</h1>';
                //echo $element[$index["DESCRIPTION"][$i+1]]["value"];
                $tags['title']=$element[$index['TITLE'][$i+1]]['value'];
                $tags['content']=$element[$index['DESCRIPTION'][$i+1]]['value'];
                $tags['content']=str_replace("<img ", "<img class=border ", $tags['content']);
                $tags['content'].="<div><a class=button href=\"".$element[$index['LINK'][$i+1]]['value']."\"> Подробнее ... </a></div>";
                $content.=App::$template->parse('news_rss', $tags);
        }

        return $content;
    }
}
