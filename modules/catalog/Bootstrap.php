<?php

namespace modules\catalog;

use classes\App;

class Bootstrap
{
    public function bootstrap(): void
    {
        App::$template->addPath('modules/catalog/views');

        App::$routing->addRoutes([
            'catalog' => [
                'pattern' => '^catalog\/$',
                'controller' => 'modules\catalog\controllers\Controller',
                'action' => 'index',
            ],
            'catalog-part' => [
                'pattern' => '^catalog\/(.*)\/$',
                'controller' => 'modules\catalog\controllers\Controller',
                'action' => 'part-list',
                'params' => [
                    '0' => 'uri',
                ]
            ],
            'catalog-item' => [
                'pattern' => '^catalog\/(.*)\/(.*)$',
                'controller' => 'modules\catalog\controllers\Controller',
                'action' => 'item',
                'params' => [
                    '0' => 'uri',
                    '1' => 'item_title',
                ]
            ],
            'catalog-load-image' => [
                'pattern' => '^catalog\/load-image$',
                'controller' => 'modules\catalog\controllers\Controller',
                'action' => 'load-image'
            ],
            'basket' => [
                'pattern' => '^basket\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\BasketController',
            ],
            'price' => [
                'pattern' => '^price\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\PriceController',
            ],

            /* For admin module */

            'catalog-part-edit' => [
                'pattern' => '^admin\/catalog\-edit\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\PartEditController',
            ],
            'catalog-item-edit' => [
                'pattern' => '^admin\/catalog\-edit\/items\/(\d+)\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\ItemEditController',
                'params' => [
                    '0' => 'part_id',
                ]
            ],
            'catalog-items-props-edit' => [
                'pattern' => '^admin\/items\-props\-edit\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\ItemsPropsEditController',
            ],
            'discount-edit' => [
                'pattern' => '^admin\/discount\-edit\/[\w\-]*$',
                'controller' => 'modules\catalog\controllers\DiscountEditController',
            ],
        ]);

        function cat_prev_part($prev_id, $deep, $array)
        {
            $query = "SELECT id,title,prev_id,seo_alias FROM cat_part WHERE id='{$prev_id}' order by title asc";
            $result = App::$db->query($query);
            $array[$deep] = $result->fetch_array();
            if ($array[$deep]['prev_id']) {
                $array = cat_prev_part($array[$deep]['prev_id'], $deep + 1, $array);
            }
            return $array;
        }

        App::$routing->addGetUrlFunction('cat_part', function ($part_id, $row) {
            if (!$part_id && array_key_exists('id', $row)) {
                $part_id = $row['id'];
            }
            $uri = 'catalog/';
            if ($part_id) {
                $array = [];
                $array = cat_prev_part($part_id, 0, $array);
                $array = array_reverse($array);
                foreach ($array as $row) {
                    $uri.=$row['seo_alias'] . '/';
                }
            }
            return $uri;
        });
    }
}
