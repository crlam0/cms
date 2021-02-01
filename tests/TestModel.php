<?php

namespace tests;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class TestModel extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'name',
            'value',
            'comment'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name'], 'string', ['min' => 1, 'max' => 8]],
            [['value'], 'integer', ['min' => 1, 'max' => 8]],
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
