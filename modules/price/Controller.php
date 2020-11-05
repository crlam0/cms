<?php

namespace modules\price;

use classes\BaseController;
use classes\App;

/**
 * Controller for module 'price'.
 *
 * @author BooT
 */
class Controller extends BaseController 
{
    
    public function actionIndex(): string 
    {
        $this->title = 'Прайс-лист';
        $this->breadcrumbs[] = ['title'=>$this->title];
        
        $this->tags['INCLUDE_JS'] .= '<script type="text/javascript" src="' . App::$SUBDIR . 'modules/price/price.js"></script>' . "\n";
        
        $query = "select content from article_item where seo_alias='before_price'";
        list($before_price) = App::$db->getRow($query);
        $content .= $before_price . "<br />";

        $tags['subparts'] = $this->sub_part(0, 0, 2);        

        $query = "SELECT cat_part.*from cat_part where prev_id='0' order by cat_part.num,cat_part.title asc";
        $result = App::$db->query($query);
        return App::$template->parse('price_index.html.twig', $tags, $result);
    }
    
    private function part_items(array $row): string 
    {
        $content = '';

        $query = "select cat_item.*,fname,cat_item.id as item_id,cat_item_images.id as image_id from cat_item 
        left join cat_item_images on (cat_item_images.id=default_img or cat_item_images.item_id=cat_item.id)
        where part_id='{$row['id']}'
        group by cat_item.id
        order by num,title asc";
        $result = App::$db->query($query);
        if ($result->num_rows) {
            $content .= App::$template->parse('price_items', $row, $result);
        }
        return $content;
    }
    
    private function sub_part(int $prev_id, int $deep, int $max_deep): string 
    {
        $query = "SELECT cat_part.*,count(cat_item.id) as cnt from cat_part left join cat_item on (cat_item.part_id=cat_part.id) where prev_id='{$prev_id}' group by cat_part.id order by cat_part.num,cat_part.title asc";
        $result = App::$db->query($query);
        $content = '';
        while ($row = $result->fetch_array()) {
            if (($deep) || ($prev_id)) {
                unset($row['seo_alias']);
            }
            $content .= $this->part_items($row);

            if ($deep < $max_deep){
                $this->sub_part($row['id'], $deep + 1, $max_deep);
            }
        }
        return $content;
    }

}
