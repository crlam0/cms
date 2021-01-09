<?php

namespace modules\article\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class ArticleList extends BaseModel {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_list';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'date_add',
            'date_change',
            'title',
            'seo_alias',
            'descr',
            'uid',
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
