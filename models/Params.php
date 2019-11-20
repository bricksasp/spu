<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_params}}".
 *
 */
class Params extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_params}}';
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
            [['sort', 'user_id'], 'integer'],
            [['value'], 'checkValue'],
            [['name'], 'string', 'max' => 32],
            [['type'], 'string', 'max' => 10],
            [['name', 'name', 'type'], 'required'],
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
            'value' => 'Value',
            'type' => 'Type',
        ];
    }

    /**
     * 
     * @OA\Schema(
     *   schema="params",
     *   description="商品参数结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="paramame1", type="array", description="key-vals(数组)", @OA\Items(
     *         @OA\Property(property="0", type="string", description="值1"),
     *         @OA\Property(property="1", type="string", description="值2"),
     *       )),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="paramame2", type="array", description="key-vals(数组)", @OA\Items(
     *         @OA\Property(property="0", type="string", description="值1"),
     *         @OA\Property(property="1", type="string", description="值2"),
     *       )),
     *     ),
     *   }
     * )
     */

    public function checkValue()
    {
        if (!is_array($this->value)) $this->addError('value', '必须为数组');
        else $this->value = json_encode(array_filter($this->value),JSON_UNESCAPED_UNICODE);
    }
}
