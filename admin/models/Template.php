<?php

namespace admin\models;

use classes\BaseModel;

/**
 * Model for table templates.
 *
 * @author BooT
 */
class Template extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'templates';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'name',
            'content',
            'comment',
            'uri',
            'file_name',
            'template_type',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', ['min' => 1, 'max' => 255]],
            [['uri', 'file_name', 'template_type'], 'string'],
            [['content', 'comment'], 'text'],
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
            'content' => 'Содержимое',
            'comment' => 'Коментарий',
            'uri' => 'URI',
            'file_name' => 'Имя файла',
            'template_type' => 'Тип',
        ];
    }

    public function getType(): string
    {
        return ucfirst($this->template_type);
    }
}
