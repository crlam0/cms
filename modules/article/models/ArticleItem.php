<?php

namespace modules\article\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class ArticleItem extends BaseModel {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_item';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'list_id',
            'active',
            'date_add',
            'date_change',
            'title',
            'seo_alias',
            'content',
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
            [['content'], 'text'],
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
            'author' => 'Автор',
            'content' => 'Описание',
        ];
    }
    
}
