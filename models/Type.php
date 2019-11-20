<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for table "{{%goods_type}}".
 *
 */
class Type extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_type}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
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
            [['data'], 'checkValue'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
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
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function checkValue()
    {
        if (is_array($this->data) && array_filter($this->data)) $this->data = json_encode($this->data,JSON_UNESCAPED_UNICODE);
        else $this->data = '';
    }

    public function getParams()
    {
        return $this->hasMany(TypeParams::className(), ['type_id' => 'id']);
    }

    public function getParamsItems()
    {
        return $this->hasMany(Params::className(), ['id' => 'params_id'])
                    ->via('params');
    }

    public function getSpecs()
    {
        return $this->hasMany(TypeSpec::className(), ['type_id' => 'id']);
    }

    public function getSpecItems()
    {
        return $this->hasMany(Spec::className(), ['id' => 'spec_id'])
                    ->via('specs');
    }

    public function getSpecValueItems()
    {
        return $this->hasMany(SpecValue::className(), ['spec_id' => 'id'])
                    ->via('specItems');
    }
}
