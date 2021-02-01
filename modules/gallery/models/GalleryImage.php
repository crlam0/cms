<?php

namespace modules\gallery\models;

use classes\BaseModel;

/**
 * Model for table settings.
 *
 * @author BooT
 */
class GalleryImage extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gallery_images';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'gallery_id',
            'date_add',
            'date_change',
            'uid',
            'title',
            'descr',
            'file_name',
            'file_type',
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
