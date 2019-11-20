<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_spec_value}}".
 *
 */
class SpecValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_spec_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spec_id', 'sort'], 'integer'],
            [['value'], 'string', 'max' => 64],
            [['sort'], 'default', 'value' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'spec_id' => 'Spec ID',
            'value' => 'Value',
            'sort' => 'Sort',
        ];
    }
}
