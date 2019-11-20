<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_product}}".
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_product}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \bricksasp\helpers\behaviors\SnBehavior::className(),
                'attribute' => 'pn',
                'type' => \bricksasp\helpers\behaviors\SnBehavior::SN_PRODUCT,
            ],
            [
                'class' => \bricksasp\helpers\behaviors\SnBehavior::className(),
                'attribute' => 'barcode',
                'type' => \bricksasp\helpers\behaviors\SnBehavior::SN_PRODUCT_BARCODE,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'marketable', 'stock', 'freeze_stock', 'is_default', 'status'], 'integer'],
            [['price', 'costprice', 'mktprice', 'weight', 'volume'], 'number'],
            [['specs'], 'string', 'max' => 255],
            [['barcode'], 'string', 'max' => 64],
            [['pn'], 'string', 'max' => 30],
            [['goods_id'], 'required'],
            [['marketable', 'status'], 'default', 'value' => 1],
            [['price', 'costprice', 'mktprice'], 'default', 'value' => 0],
            [['is_default', ], 'default', 'value' => 2],
        ];
    }


    /**
     * @OA\Schema(
     *  schema="product",
     *  description="单品结构",
     *  @OA\Property(property="stock", type="integer", description="库存"),
     *  @OA\Property( property="price", type="string", description="售价"),
     *  @OA\Property( property="costprice", type="string", description="成本价" ),
     *  @OA\Property( property="mktprice", type="string", description="原价" ),
     *  @OA\Property( property="weight", type="string", description="重量" ),
     *  @OA\Property( property="volume", type="string", description="体积" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'barcode' => 'Barcode',
            'pn' => 'Pn',
            'price' => 'Price',
            'costprice' => 'Costprice',
            'mktprice' => 'Mktprice',
            'marketable' => 'Marketable',
            'stock' => 'Stock',
            'freeze_stock' => 'Freeze Stock',
            'specs' => 'Spes Desc',
            'is_default' => 'Is default',
            'status' => 'Status',
            'weight' => 'Weight',
            'volume' => 'Volume',
        ];
    }

    /**
     * 商品信息
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id'])->select(['id', 'name', 'image_id', 'gn']);
    }
}
