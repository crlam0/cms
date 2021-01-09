<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class menuList extends BaseModel {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_list';
    }
    
    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'title',
            'root',
            'top_menu',
            'bottom_menu',
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
        ];
    }
    
}
