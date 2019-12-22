<?php

namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_comment_image}}".
 * 
 */
class GoodsCommentImage extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_comment_image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment_id', 'sort'], 'integer'],
            [['image_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'image_id' => 'Image ID',
            'sort' => 'Sort',
        ];
    }
}
