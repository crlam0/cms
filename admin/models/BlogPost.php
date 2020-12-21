<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class BlogPost extends BaseModel {
    
    public static $fields = [
        'id',
        'date_add',
        'uid',
        'title',
        'seo_alias',
        'content',
        'active',
        'target_type',
        'target_id',
        'href',
        'image_name',
        'image_type',
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog_posts';
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
            [['uid'], 'integer'],
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
        ];
    }
    
}
