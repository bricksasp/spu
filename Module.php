<?php
namespace bricksasp\spu;

use Yii;

/**
 * Module module definition class
 */
class Module extends \bricksasp\base\BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'bricksasp\spu\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['spu'])) {
            Yii::$app->i18n->translations['spu'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'zh-cn',
                'basePath' => '@bricksasp/base/messages',
            ];
        }
    }
}
