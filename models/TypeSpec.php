<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_type_spec}}".
 *
 */
class TypeSpec extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_type_spec}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spec_id'], 'required'],
            [['spec_id', 'type_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'spec_id' => 'Spec ID',
            'type_id' => 'Type ID',
        ];
    }
}
