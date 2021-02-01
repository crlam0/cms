<?php

namespace modules\media\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class MediaFile extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_files';
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
            'uid',
            'title',
            'descr',
            'file_name',
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
            'descr' => 'Описание',
        ];
    }
}
