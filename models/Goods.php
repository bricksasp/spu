<?php
namespace bricksasp\spu\models;

use Yii;
use bricksasp\helpers\Tools;
use bricksasp\base\models\Brand;
use bricksasp\base\models\Label;
use bricksasp\base\models\File;
use bricksasp\base\Config;

/**
 * This is the model class for table "{{%goods}}".
 */
class Goods extends \bricksasp\base\BaseActiveRecord
{
    const SINGLE_PRODUCT = 1;
    const SPEC_PRODUCT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            [
                'class' => \bricksasp\helpers\behaviors\SnBehavior::className(),
                'attribute' => 'gn',
                'type' => \bricksasp\helpers\behaviors\SnBehavior::SN_GOODS,
            ],
            [
                'class' => \bricksasp\helpers\behaviors\VersionBehavior::className()
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
            [['price', 'costprice', 'mktprice'], 'number'],
            [['type', 'is_stock_check', 'cat_id', 'type_id', 'brand_id', 'is_nomal_virtual', 'is_on_shelves', 'on_shelves_time', 'off_shelves_time', 'comments_count', 'view_count', 'buy_count', 'sell_count', 'sort', 'is_recommend', 'is_hot', 'status', 'user_id', 'version', 'created_at', 'updated_at'], 'integer'],
            [['content', 'specs', 'params'], 'string'],
            [['gn'], 'string', 'max' => 30],
            [['name', 'brief', 'keywords'], 'string', 'max' => 255],
            [['stock_unit', 'weight_unit', 'volume_unit'], 'string', 'max' => 20],
            
            [['name', 'cat_id', 'type_id'],'required'],
            [['comments_count', 'view_count', 'buy_count', 'sell_count'], 'default', 'value' => 0],
            [['is_nomal_virtual', 'status', 'version'], 'default', 'value' => 1],
            [['sort'], 'default', 'value' => 100],
            [['type', ], 'default', 'value' => self::SINGLE_PRODUCT],
            [['is_stock_check', 'is_on_shelves'], 'default', 'value' => 1],
            [['video', 'image_id'], 'safe'],
            [['imageItems'],\bricksasp\helpers\validators\ArrayValidator::className()]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gn' => 'Gn',
            'name' => 'Name',
            'brief' => 'Brief',
            'price' => 'Price',
            'costprice' => 'Costprice',
            'mktprice' => 'Mktprice',
            'image_id' => 'Image ID',
            'cat_id' => 'Cat ID',
            'type_id' => 'Type ID',
            'brand_id' => 'Brand ID',
            'is_nomal_virtual' => 'Is Nomal Virtual',
            'is_on_shelves' => 'Is On Shelves',
            'on_shelves_time' => 'On Shelves Time',
            'off_shelves_time' => 'Off Shelves Time',
            'stock' => 'Stock',
            'freeze_stock' => 'Freeze Stock',
            'volume' => 'Volume',
            'weight' => 'Weight',
            'weight_unit' => 'Weight Unit',
            'volume_unit' => 'Volume Unit',
            'content' => 'Content',
            'specs' => 'Spes Desc',
            'params' => 'Params',
            'keywords' => 'Keywords',
            'comments_count' => 'Comments Count',
            'view_count' => 'View Count',
            'buy_count' => 'Buy Count',
            'sell_count' => 'Sell Count',
            'sort' => 'Sort',
            'is_recommend' => 'Is Recommend',
            'is_hot' => 'Is Hot',
            'status' => 'Status',
            'user_id' => 'User ID',
            'version' => 'Version',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getProductItems()
    {
        return $this->hasMany(Product::className(), ['goods_id' => 'id']);
    }

    public function getBrandItem()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id'])->select(['id', 'name', 'logo']);
    }

    /**
     * 封面图片 商品图片 第一张
     */
    public function getCoverItem()
    {
        return $this->hasOne(File::className(), ['id' => 'image_id'])->select(['id', 'file_url']);
    }

    /**
     * 视频地址
     */
    public function getVideoItem()
    {
        return $this->hasOne(File::className(), ['id' => 'video'])->select(['id', 'file_url', 'name', 'ext']);
    }

    /**
     * 商品图片
     */
    public function getImageRelation()
    {
        return $this->hasMany(GoodsImage::className(), ['goods_id' => 'id']);
    }

    public function getImageItems()
    {
        return $this->hasMany(File::className(), ['id' => 'image_id'])->via('imageRelation')->select(['id', 'file_url', 'name', 'ext']);
    }

    public function getLabels()
    {
        return $this->hasMany(GoodsLabel::className(), ['goods_id' => 'id']);
    }

    public function getLabelItems()
    {
        return $this->hasMany(Label::className(), ['id' => 'lable_id'])->via('labels')->select(['id', 'name', 'style']);
    }

    public function getCommentItems()
    {
        return $this->hasMany(GoodsComment::className(), ['goods_id' => 'id'])/*->onCondition(['cat_id' => 1])*/;
    }

    /**
     * 添加商品
     * @param  array  $data 
     * @return bool
     */
    public function saveGoods($data=[])
    {
        list($data, $productItems, $imageItems) = $this->formatData($data);
        $this->load($data,'');

        $transaction = self::getDb()->beginTransaction();
        try {
            if ($this->type == self::SPEC_PRODUCT) {
                $this->specs = json_encode($this->getGoodsSpec($this->type_id, 2),JSON_UNESCAPED_UNICODE);
                $this->params = json_encode($this->getParams($this->type_id),JSON_UNESCAPED_UNICODE);
            }

            $this->save();
            if (!$this->id) {
                $transaction->rollBack();
                return false;
            }

            $images = [];
            foreach ($imageItems as $k => $v) {
                $image['goods_id'] = $this->id;
                $image['image_id'] = $v['id'];
                $image['sort'] = $k + 1;
                $images[] = $image;
            }

            self::getDb()->createCommand()
            ->batchInsert(GoodsImage::tableName(),['goods_id','image_id','sort'],$images)
            ->execute();

            foreach ($productItems as $k => $product) {
                $product['goods_id']    = $this->id;
                $model = new Product();
                $model->load($product,'');
                $model->save();
            }
            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 更新视频
     * @param  array  $data 
     * @return bool
     */
    public function updateGoods($data=[])
    {
        $oldAttributes = $this->oldAttributes;
        list($data, $productItems, $imageItems) = $this->formatData($data);
        $this->load($data,'');

        $transaction = self::getDb()->beginTransaction();
        try {
            if ($this->type == self::SPEC_PRODUCT && $oldAttributes['type_id'] != $this->type_id) {
                $this->specs = json_encode($this->getGoodsSpec($this->type_id, 2),JSON_UNESCAPED_UNICODE);
                $this->params = json_encode($this->getParams($this->type_id),JSON_UNESCAPED_UNICODE);
                // 更改类型删除所有单品
                Product::deleteAll(['goods_id'=>$this->id]);
            }
            // 保存商品
            if ($this->save() === false) {
                $transaction->rollBack();
                return false;
            }
            $images = [];
            foreach ($imageItems as $k => $v) {
                $image['goods_id'] = $this->id;
                $image['image_id'] = $v['id'];
                $image['sort'] = $k + 1;
                $images[] = $image;
            }

            GoodsImage::deleteAll(['goods_id'=>$this->id]);
            self::getDb()->createCommand()
            ->batchInsert(GoodsImage::tableName(),['goods_id','image_id','sort'],$images)
            ->execute();
            // 保存单品
            foreach ($productItems as $product) {
                if (empty($product['id'])) {
                    $product['goods_id']    = $this->id;
                    $model = new Product();
                    $model->load($product,'');
                    $model->save();
                }else{
                    $model = Product::findOne($product['id']);
                    $model->load($product, '');
                    $model->save();
                }
            }
            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }

    public function formatData($data)
    {
        $productItems = $data['productItems'];
        $imageItems = $data['imageItems'];
        $videoItem = $data['videoItem'];
        $data['cat_id'] = end($data['cat_id']);
        unset($data['productItems'], $data['imageItems'], $data['videoItem']);

        if ($data['is_on_shelves'] == 1 && $this->is_on_shelves != 1) {
            $data['on_shelves_time']= time();
        }elseif ($data['is_on_shelves'] == 2 && $this->is_on_shelves != 2) {
            $data['off_shelves_time'] = time();
        }
        
        // 设置默认单品
        $is_default = array_column($productItems, 'is_default');
        if (!in_array(1, $is_default)) {
            $productItems[0]['is_default'] = 1;
            $k = 0;
        }else{
            $k = array_search(1, $is_default);
        }
        $data['price'] = $productItems[$k]['price'];
        $data['costprice'] = $productItems[$k]['costprice'];
        $data['mktprice'] = $productItems[$k]['mktprice'];
        $data['image_id'] = $imageItems[0]['id'] ?? '';
        return [$data, $productItems, $imageItems];
    }

    /**
     * 商品规格属性
     * @param  integer $type_id 类型id
     * @param  integer $sence 1 product 2 goods
     * @return array           
     */
    public static function getGoodsSpec($type_id=0 , $sence=1)
    {
        $item_spec = Type::find()->with(['specItems'])->where(['id'=> $type_id])->one();
        if (!$item_spec) return null;
        $input = [];

        if ($sence == 2) {
            foreach ($item_spec->specItems as $spec) {
                foreach ($spec->items as $val) {
                    $input[$spec['name']][] = $val['value'];
                }
            }
            return $input;
        }

        foreach ($item_spec->specItems as $spec) {
            $spec_v = [];
            foreach ($spec->items as $val) {
                $spec_v[$val['value']] = $spec['name'];
            }
            $input[] = $spec_v;
        }
        $input = array_filter($input);
        if (empty($input)) Tools::exceptionBreak('类型数据不全');
        // 拼接格式
        $spec_product = Tools::cartesian($input,function ($v,$v2=[])
        {
            if ($v2) {
                return $v[1] . ',' . $v2[1] . ':' . $v2[0];
            }
            return $v[1] . ':' . $v[0];
        });
        return $spec_product;
    }

    /**
     * 商品参数
     * @param  integer $type_id 类型id
     * @return array           
     */
    public static function getParams($type_id=0)
    {
        $item_parmas = Type::find()->with(['paramsItems'])->where(['id'=> $type_id])->one();
        $output = [];
        foreach ($item_parmas->paramsItems as $v) {
            $output[$v['name']] = json_decode($v['value'],true);
        }
        return $output;
    }

    /**
     * 商品详情
     * @OA\Schema(
     *  schema="goodsDetal",
     *  description="商品详情结构",
     *  @OA\Property( property="name", type="string", description="商品名称"),
     *  @OA\Property( property="brief", type="string", description="商品简介" ),
     *  @OA\Property( property="content", type="string", description="商品内容" ),
     *  @OA\Property( property="comments_count", type="integer", description="评论数" ),
     *  @OA\Property( property="view_count", type="integer", description="浏览数" ),
     *  @OA\Property( property="stock_unit", type="integer", description="库存单位" ),
     *  @OA\Property( property="weight_unit", type="integer", description="重量单位" ),
     *  @OA\Property( property="volume_unit", type="integer", description="体积单位" ),
     *  @OA\Property( property="imageItems", type="array", description="商品图片", @OA\Items(ref="#/components/schemas/file")),
     *  @OA\Property( property="labelItems", type="array", description="商品标签", @OA\Items(ref="#/components/schemas/label")),
     *  @OA\Property( property="brandItem", description="品牌", ref="#/components/schemas/brand"),
     *  @OA\Property( property="videoItem", description="视频介绍", ref="#/components/schemas/file"),
     *  @OA\Property( property="default_product", description="默认单品", ref="#/components/schemas/product"),
     *  @OA\Property( property="params", description="商品 参数名称-值", ref="#/components/schemas/params"),
     *  @OA\Property( property="specs", description="商品 属性名称-值", ref="#/components/schemas/specs"),
     * )
     */
    public static function goodsDetal($goods_id,$product_id=0)
    {
        $goods = Goods::find()
            ->with(['productItems', 'labelItems', 'brandItem', 'imageItems', 'videoItem'])
            ->select(['id','type', 'brand_id', 'name', 'brief', 'content', 'params', 'comments_count', 'view_count', 'comments_count','video','image_id', 'stock_unit', 'weight_unit', 'volume_unit'])
            ->where(['id'=>$goods_id, ])
            ->one();
        if (empty($goods)) return null;

        if ($goods->type == self::SPEC_PRODUCT) {
            if ($product_id) {
                $k = array_search($product_id, array_column($goods->productItems, 'id'));
            }else {
                $k = array_search(1, array_column($goods->productItems, 'is_default'));
            }
            $default_product = $goods->productItems[$k];
            $data['default_product'] = $default_product;
            $data['specs'] = self::defaultSpec($goods->productItems,$default_product->specs);
        }else{
            $data['default_product'] = $goods->productItems[0];
            $data['specs'] = [];
        }
        $data = array_merge($goods->toArray(), $data);
        $data['params'] = json_decode($data['params'],true);
        $data['labelItems'] = $goods->labelItems;
        $data['brandItem'] = $goods->brandItem;
        $data['videoItem'] = $goods->videoItem ? Tools::format_array($goods->videoItem, ['file_url'=>['implode',['',[Config::instance()->web_url,'###']],'array']]) : (object)[];
        $imageItems = Tools::format_array($goods->imageItems, ['file_url'=>['implode',['',[Config::instance()->web_url,'###']],'array']], 2);

        $imgs = array_column($imageItems, 'id');
        $sort = array_column($goods->imageRelation, 'sort', 'image_id');
        $k = [];

        foreach ($imgs as $v) {
            $k[] = $sort[$v];
        }

        $imageItems = array_combine($k, $imageItems);
        ksort($imageItems);
        $data['imageItems'] = array_values($imageItems);
        $data['content'] = str_replace('src="file', 'src="' . Config::instance()->web_url . 'file', $data['content']);
        return $data;
    }

    /**
     * 商品默认属性
     * @param  array $products 
     * @return array 
     */
    public static function defaultSpec($data=[],$default_specs=[])
    {
        $spes = [];
        foreach ($data as $product) {
            $item = explode(',', $product->specs);
            foreach ($item as $v) {
                $kv = explode(':', $v);
                $spec['product_id'] = $product->id;
                $spec['spec'] = $kv[1];
                if ($default_specs == $product->specs) $spec['default'] = 1; else $spec['default'] = 0;
                $spes[$kv[0]][] = $spec;
            }
        }
        return $spes;
    }
}
