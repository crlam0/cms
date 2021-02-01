<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class Message extends BaseModel {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'name',
            'type',
            'content',
            'comment'
        ];
    }    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name'], 'string', ['min' => 1, 'max' => 255]],
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
            'name' => 'Название',
            'type' => 'Тип',
            'content' => 'Содержание',
            'comment' => 'Коментарий',
        ];
    }
    
    public $types = [
        'info', 
        'notice', 
        'error',
    ];
    
}
