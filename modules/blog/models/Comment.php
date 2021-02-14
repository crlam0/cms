<?php

namespace modules\blog\models;

use classes\BaseModel;

/**
 * Model for table menu_item.
 *
 * @author BooT
 */
class Comment extends BaseModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * @inheritdoc
     */
    public static function fields()
    {
        return [
            'id',
            'target_type',
            'target_id',
            'date_add',
            'uid',
            'active',
            'content',
            'email',
            'author',
            'ip',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_type', 'target_id'], 'required'],
            [['content'], 'string', ['min' => 1, 'max' => 1024]],
            [['target_id'], 'integer'],
            [['email', 'author'], 'string', ['min' => 1, 'max' => 64]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-Mail',
            'author' => 'Автор',
            'content' => 'Содержание',
        ];
    }
}
