<?php

namespace modules\catalog\controllers;

use classes\App;
use classes\BaseController;

use modules\catalog\models\CatalogPart;
use modules\catalog\models\CatalogItem;

class ItemsPropsEditController extends BaseController
{
    private $image_path;
    private $image_width;
    private $image_height;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Прайс лист';
        $this->breadcrumbs[] = ['title' => $this->title];
        $this->user_flag = 'admin';
    }

    public function actionIndex(): string
    {
        $content = '';
        $this->subPart($content, 0, 0, 2);

        return $this->render('catalog_edit_items_props.html.twig', ['content' => $content]);
    }
    
    private function subPart(&$content, $prev_id, $deep, $max_deep): void
    {
        $model = new CatalogPart;
        $result = $model->findAll(['prev_id' => $prev_id], 'num, title asc');
        
        while ($row = $result->fetch_array()) {
            if ((!$deep) && (!$prev_id)) {
                $content .= "<h3>{$row['title']}</h3>";
            } else {
                $content .= "<h4>{$row['title']}</h4>";
            }
            $content .= $this->partItems($row['id']);
            if ($deep < $max_deep) {
                $this->subPart($content, $row['id'], $deep + 1, $max_deep);
            }
        }
    }
    
    private function partItems($part_id): string
    {
        $input = App::$input;
        $content = '';
        $query = "select cat_item.*,cat_item.id as item_id,cat_part.items_props
            from cat_item
            left join cat_part on (cat_part.id=part_id)
            where part_id=?
            group by cat_item.id
            order by num,title asc";
        $result = App::$db->query($query, ['part_id' => $part_id]);
        $content = '<table class="table table-striped table-responsive table-bordered">';
        if ($result->num_rows) {
            $content .= '<tr>';
            $tags = $result->fetch_array();
            $content .= '<td>Название</td>'. '<td>Базовая цена</td>' . PHP_EOL;
            if (strlen($tags['items_props'])) {
                $props_array = json_decode($tags['items_props'], true);
                // print_array($props_array);
                if (!is_array($props_array)) {
                    $content.=App::$message->get('', [], 'Массив свойств неверен');
                } else {
                    $props_values = json_decode($tags['props'], true);
                    // print_array($props_values);
                    if (is_array($props_values)) {
                        foreach ($props_values as $input_name => $value) {
                            $param_value[$input_name] = $value;
                        }
                    }
                    foreach ($props_array as $input_name => $params) {
                        $content .= '<td align="center">'.$params['name'].'</td>' . PHP_EOL;
                    }
                }
            }
            $result->data_seek(0);
            $content .= '</tr>' . PHP_EOL;
            while ($tags = $result->fetch_array()) {
                $content .= '<tr><td width="300">'.$tags['title'].'</td>';
                $content .= '<td><input type="edit" class="form-control attr_change" maxlength="8" size="4" id="'.$tags['id'].'" attr_type="simple" attr_name="price" value="'.$tags['price'].'"></td>';
                // echo $tags['items_props'];
                if (strlen($tags['items_props'])) {
                    $props_array = json_decode($tags['items_props'], true);
                    // print_array($props_array);
                    if (!is_array($props_array)) {
                        $content .= App::$message->get('', [], 'Массив свойств неверен');
                    } else {
                        $props_values = json_decode($tags['props'], true);
                        // print_array($props_values);
                        $param_value = [];
                        if (is_array($props_values)) {
                            foreach ($props_values as $input_name => $value) {
                                $param_value[$input_name] = $value;
                            }
                        }
                        foreach ($props_array as $input_name => $params) {
                            $content .= '<td align="center">' . PHP_EOL;
                            if (check_key('attr_type', $params) == 'simple') {
                                $content .= '<input type="edit" class="form-control attr_change" maxlength="8" size="4" id="'.$tags['id'].'" attr_type="simple" attr_name="'.$params['attr_name'].'" value="'.$tags[$params['attr_name']].'">';
                                
                            } elseif (check_key('type', $params) == 'boolean') {
                                $content .= '<input type="checkbox" class="attr_change" size="8" id="'.$tags['id'].'"  attr_type="boolean" attr_name="'.$input_name.'" '.(check_key($input_name, $param_value) ? ' checked' : '').'>';
                            } else {
                                $content .= '<input type="edit" class="form-control attr_change" maxlength="8" size="4" id="'.$tags['id'].'" attr_type="string" attr_name="'.$input_name.'" value="'.check_key($input_name, $param_value).'">';
                            }
                            $content .= '</td>' . PHP_EOL;
                        }
                    }
                }
            }
        }
        $content .= '</table>';
        return $content;
    }
    

    public function actionChange(int $item_id, string $attr_type, string $attr_name, string $value): string
    {
        $model = new CatalogItem($item_id);
        if ($attr_type == 'simple') {
            $model[$attr_name] = $value;
            $model->save(false);
        } elseif ($attr_type == 'string' || $attr_type == 'boolean') {
            if (!$props_values = my_json_decode($model->props)) {
                $props_values=[];
            }
            $props_values[$attr_name] = $value;
            $model->props = json_encode($props_values);
            $model->save(false);
        }
        echo 'OK';
        exit();
    }

}
