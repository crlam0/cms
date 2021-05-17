<?php

namespace modules\catalog\models;

use classes\App;
use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class CatalogItem extends BaseModel
{    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cat_item';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'part_id',
            'active',
            'num',
            'date_add',
            'date_change',
            'uid',
            'title',
            'seo_alias',
            'descr',
            'descr_full',
            'price',
            'cnt_weight',
            'props',
            'default_img',
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
            [['num'], 'integer']
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
            'num' => 'Номер',
            'author' => 'Автор',
            'descr' => 'Описание',
            'descr_full' => 'Полное описание',
            'price' => 'Цена',
            'cnt_weight' => 'Кол-во, вес',
        ];
    }

    public function getImages() : array
    {
        $images = App::$db->findAll('cat_item_images', ['item_id' => $this->id]);
        return $images->fetch_all(MYSQLI_ASSOC);
    }

    public function getDefaultImage() : array
    {
        $images = App::$db->findAll('cat_item_images', ['id' => $this->default_img]);
        return $images->fetch_all(MYSQLI_ASSOC);
    }
    

    
}
