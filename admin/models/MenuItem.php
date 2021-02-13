<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table menu_item.
 *
 * @author BooT
 */
class MenuItem extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_item';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'menu_id',
            'active',
            'flag',
            'position',
            'title',
            'css_class',
            'href',
            'submenu_id',
            'target_type',
            'target_id',
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
            [['menu_id', 'target_id', 'submenu_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'position' => 'Позиция',
            'title' => 'Название',
            'css_class' => 'Рисунок',
            'title' => 'Название',
            'title' => 'Название',
            'title' => 'Название',
        ];
    }
}
