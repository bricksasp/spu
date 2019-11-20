<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_spec}}".
 *
 */
class Spec extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_spec}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \bricksasp\helpers\behaviors\UidBehavior::className(),
                'createdAtAttribute' => 'user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
            [['sort', 'user_id'], 'integer'],
            [['sort'], 'default', 'value' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sort' => 'Sort',
        ];
    }


    /**
     * 
     * @OA\Schema(
     *   schema="specs",
     *   description="商品属性结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="specname1", type="array", description="key-vals(数组)", @OA\Items(
     *          @OA\Property(property="product_id", type="integer", description="单品id"),
     *          @OA\Property(property="spec", type="string", description="属性值"),
     *          @OA\Property(property="default", type="string", description="是否默认"),
     *       )),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="specname2", type="array", description="key-vals(数组)", @OA\Items(
     *          @OA\Property(property="product_id", type="integer", description="单品id"),
     *          @OA\Property(property="spec", type="string", description="属性值"),
     *          @OA\Property(property="default", type="string", description="是否默认"),
     *       )),
     *     ),
     *   }
     * )
     */

    public function getItems()
    {
        return $this->hasMany(SpecValue::className(), ['spec_id' => 'id']);
    }
}
