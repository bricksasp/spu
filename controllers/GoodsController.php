<?php
namespace bricksasp\spu\controllers;

use Yii;
use bricksasp\spu\models\Goods;
use bricksasp\spu\models\GoodsSearch;
use bricksasp\spu\models\GoodsImage;
use bricksasp\spu\models\Product;
use bricksasp\spu\models\GoodsLabel;
use bricksasp\spu\models\Category;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use bricksasp\helpers\Tools;
use bricksasp\base\Config;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends BaseController
{


    /**
     * 登录可访问 其他需授权
     * @return array
     */
    public function allowAction()
    {
        return [
            'create',
            'update',
            'delete',
            'view',
            'spec'
        ];
    }

    /**
     * 免登录可访问
     * @return array
     */
    public function allowNoLoginAction()
    {
        return [
            'index',
            'detail',
            'view',
        ];
    }

    /**
     * @OA\Get(path="/goods/index",
     *   summary="商品列表",
     *   tags={"spu模块"},
     *   @OA\Parameter(
     *     description="开启平台功能后，访问商户对应的数据标识，未开启忽略此参数",
     *     name="access-token",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="当前叶数",
     *     name="page",
     *     in="query",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="每页行数",
     *     name="pageSize",
     *     in="query",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="列表数据",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/goodslist"),
     *     ),
     *   ),
     * )
     *
     * @OA\Schema(
     *   schema="goodslist",
     *   description="商品列表结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="id", type="integer", description="商品id"),
     *       @OA\Property(property="name", type="string", description="商品名称"),
     *       @OA\Property( property="price", type="string", description="售价"),
     *       @OA\Property( property="costprice", type="string", description="成本价" ),
     *       @OA\Property( property="mktprice", type="string", description="原价" ),
     *       @OA\Property(property="labelItems", type="array", @OA\Items(ref="#/components/schemas/label"), description="商品标签"),
     *       @OA\Property(property="coverItem", ref="#/components/schemas/file", description="封面图")
     *     )
     *   }
     * )
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search($this->queryFilters());
        return $this->pageFormat($dataProvider,['labelItems'=>false,'labels'=>false, 'coverItem'=>[
            ['file_url'=>['implode',['',[Config::instance()->web_url,'###']],'array']]
        ]]);
    }

    /**
     * Displays a single Goods model.
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $model = Goods::find()->with(['imageItems','productItems','videoItem',/*'coverItem','brandItem'*/])->where(['id'=>Yii::$app->request->get('id')])->one();
        if (!$model) Tools::exceptionBreak(Yii::t('base',40001));

        $rela = $model->relatedRecords;
        $rela['videoItem'] = $rela['videoItem'] ? Tools::format_array($rela['videoItem'], ['file_url'=>['implode',['',[Config::instance()->web_url,'###']],'array']]):(object)[];
        $imageItems = Tools::format_array($rela['imageItems'], ['file_url'=>['implode',['',[Config::instance()->web_url,'###']],'array']], 2);

        $imgs = array_column($imageItems, 'id');
        $sort = array_column($rela['imageRelation'], 'sort', 'image_id');
        $k = [];

        foreach ($imgs as $v) {
            $k[] = $sort[$v];
        }
        $imageItems = array_combine($k, $imageItems);
        ksort($imageItems);
        $rela['imageItems'] = array_values($imageItems);

        $goods = array_merge($model->toArray(),$rela);

        $amodel = new Category();
        $cascader = $amodel->cascader($model->cat_id);
        $goods['cat_id'] = array_column($cascader, 'id');

        return $this->success($goods);
    }


    /**
     * @OA\Get(path="/goods/detail",
     *   summary="商品详情",
     *   tags={"spu模块"},
     *   @OA\Parameter(
     *     description="用户请求token,登录后填写",
     *     name="X-Token",
     *     in="header",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="商品id",
     *     name="id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="商品详情结构",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/goodsDetal"),
     *     ),
     *   ),
     * )
     */
    public function actionDetail()
    {
        $goods = Goods::goodsDetal(Yii::$app->request->get('id'),Yii::$app->request->get('product_id'));
        if (!$goods) Tools::exceptionBreak(Yii::t('base',40001));
        return $this->success($goods);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Goods();

        if ($model->saveGoods(Yii::$app->request->post())) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));

        if ($model->updateGoods(Yii::$app->request->post())) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $transaction = Goods::getDb()->beginTransaction();
        if (is_array($id)) $n = Goods::deleteAll(['id'=>$id, 'user_id'=>$this->uid]); else $item = $this->findModel($id);
            
        try {
            Product::deleteAll(['goods_id'=>$id]);
            GoodsImage::deleteAll(['goods_id'=>$id]);
            GoodsLabel::deleteAll(['goods_id'=>$id]);
            if (is_array($id)) {
                if ($n != count($id)) {
                    $transaction->rollBack();
                    Tools::exceptionBreak(Yii::t('base',40003));
                }
            } else $item->delete();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->success($n);
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionSpec()
    {
        $spec_product = Goods::getGoodsSpec(Yii::$app->request->get('type_id'));
        return $this->success($spec_product);
    }

    public function actionSetlabel()
    {
        $goods_id = Yii::$app->request->get('id');
        $data = Yii::$app->request->post();
        $inster = [];
        foreach ($data as $k => $item) {
            $row['goods_id'] = $goods_id;
            $row['lable_id'] = $item['id'];
            $row['sort'] = $k + 1;
            $inster[] = $row;
        }
        GoodsLabel::deleteAll(['goods_id' => $goods_id]);
        $a = GoodsLabel::getDb()->createCommand()
            ->batchInsert(GoodsLabel::tableName(),['goods_id','lable_id','sort'],$inster)
            ->execute();
        return $this->success($a);
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Goods the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        }

        Tools::exceptionBreak(Yii::t('base',40001));
    }
}