<?php

namespace modules\media\models;

use classes\BaseModel;

/**
 * Model for table media_list.
 *
 * @author BooT
 */
class MediaList extends BaseModel {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_list';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'active',
            'date_add',
            'date_change',
            'uid',
            'title',
            'seo_alias',
            'descr',
            'image_name',
            'image_type',
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
        ];
    }
    
}
