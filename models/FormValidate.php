<?php
namespace bricksasp\spu\models;

use Yii;

/**
 * This is the model class for Module form validate.
 */
class FormValidate extends \bricksasp\base\FormValidate
{
    const CREATE_GOODS_COMMENT = 'create_goods_comment';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id' ], 'integer'],
            [['content' ], 'string'],
            [['image_ids', 'content', 'order_id'], 'required', 'on' => ['create_goods_comment']],
            [['image_ids'], 'checkimgs']
        ];
    }

    /**
     * 使用场景
     */
    public function scenarios()
    {
        return [
            self::CREATE_GOODS_COMMENT => ['cart', 'products', 'ship_id'],
        ];
    }

    public function checkimgs()
    {
        if(is_array($this->image_ids)){
            $this->addError('image_ids', 'image_ids 必须为数组');
        }
    }
}