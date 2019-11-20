<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "bricksasp_goods_label".
 *
 */
class GoodsLabel extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bricksasp_goods_label';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'lable_id', 'sort'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'lable_id' => 'Lable ID',
            'sort' => 'Sort',
        ];
    }
}
