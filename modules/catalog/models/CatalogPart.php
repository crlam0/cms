<?php

namespace modules\catalog\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class CatalogPart extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cat_part';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'prev_id',
            'date_add',
            'date_change',
            'uid',
            'num',
            'title',
            'seo_alias',
            'descr',
            'image_name',
            'image_type',
            'item_image_width',
            'item_image_height',
            'price_title',
            'items_props',
            'related_products',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', ['min' => 1, 'max' => 255]],
            [['descr'], 'text'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'seo_alias' => 'SEO алиас',
            'descr' => 'Описание',
            'price_title' => 'Колонка с ценой',
            'item_image_width' => 'Фиксированая ширина для фотографий товаров:',
            'item_image_height' => 'Фиксированая высота для фотографий товаров:',

        ];
    }
}
