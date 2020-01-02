<?php

namespace bricksasp\spu\models;

use Yii;
use bricksasp\order\models\Order;
use bricksasp\order\models\OrderItem;
use bricksasp\helpers\Tools;
use bricksasp\base\models\File;

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

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'user_id', 'order_id', 'goods_id', 'product_id', 'owner_id', 'score', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content', 'seller_content'], 'string'],
            [['score'], 'default', 'value' => 5],
            [['status'], 'default', 'value' => 1],
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

    /**
     * 评论图片
     */
    public function getImageRelation()
    {
        return $this->hasMany(GoodsCommentImage::className(), ['comment_id' => 'id']);
    }

    public function getImageItems()
    {
        return $this->hasMany(File::className(), ['id' => 'image_id'])->via('imageRelation')->select(['id', 'file_url', 'ext'])->asArray();
    }

    /**
     * 保存数据
     * @param  array $params 
     * @return bool         
     */
    public function saveData($params)
    {
        extract($params);

        $order = Order::find()->select(['id', 'is_comment'])->where(['id' => $order_id, 'user_id' => $user_id])->one();
        if (empty($order) || $order->is_comment == Order::ORDER_IS_COMMENT) {
            Tools::exceptionBreak(930001);
        }

        $orderItem = OrderItem::find()->select(['product_id'])->where(['order_id' => $order_id, 'goods_id' => $goods_id])->one();
        if (empty($orderItem)) {
            Tools::exceptionBreak(930002);
        }

        $params['product_id'] = $orderItem->product_id;
        $this->load($params);

        $transaction = self::getDb()->beginTransaction();
        try {
            if ($this->save() === false) {
                $transaction->rollBack();
                return false;
            }
            $images = [];
            foreach ($image_ids as $k => $v) {
                $image['comment_id'] = $this->id;
                $image['image_id'] = $v;
                $image['sort'] = $k + 1;
                $images[] = $image;
            }

            self::getDb()->createCommand()
            ->batchInsert(GoodsCommentImage::tableName(),['comment_id','image_id','sort'],$images)
            ->execute();

            $order->is_comment = Order::ORDER_IS_COMMENT;
            $order->save();
            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }
}
