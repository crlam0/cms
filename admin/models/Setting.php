<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class Setting extends BaseModel {
    
    public static $fields = [
        'id',
        'name',
        'value',
        'comment'        
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name'], 'string', ['min' => 1, 'max' => 255]],
            [['value', 'comment'], 'text'],
            [['value'], 'string', ['min' => 10, 'max' => 255]],
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
            'value' => 'Значение',
            'comment' => 'Коментарий',
        ];
    }
    
}
