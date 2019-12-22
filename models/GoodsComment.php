<?php

namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_comment}}".
 * 
 */
class GoodsComment extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_comment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'user_id', 'order_id', 'goods_id', 'product_id', 'owner_id', 'score', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content', 'seller_content'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'owner_id' => 'Owner ID',
            'score' => 'Score',
            'content' => 'Content',
            'seller_content' => 'Seller Content',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public function saveData($parmas)
    {
        // $
        // $this->load($)
    }
}
