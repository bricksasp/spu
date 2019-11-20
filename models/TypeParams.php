<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_type_params}}".
 *
 */
class TypeParams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_type_params}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['params_id', 'type_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'params_id' => 'Params ID',
            'type_id' => 'Type ID',
        ];
    }
}
