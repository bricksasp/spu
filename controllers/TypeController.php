<?php
namespace bricksasp\spu\controllers;

use Yii;
use bricksasp\spu\models\Type;
use bricksasp\spu\models\TypeSpec;
use bricksasp\spu\models\TypeParams;
use yii\data\ActiveDataProvider;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use bricksasp\helpers\Tools;

/**
 * TypeController implements the CRUD actions for Type model.
 */
class TypeController extends BaseController
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
        ];
    }

    /**
     * 免登录可访问
     * @return array
     */
    public function allowNoLoginAction()
    {
        return [
            'view',
        ];
    }

    /**
     * Lists all Type models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $dataProvider = new ActiveDataProvider([
            'query' => Type::find($this->dataOwnerUid()),
            'pagination' => [
                'pageSize' => $params['limit'] ?? 10
            ]
        ]);
        return $this->pageFormat($dataProvider);
    }

    /**
     * Displays a single Type model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $item = Type::find()->with(['paramsItems', 'specItems'])->where(['id'=> Yii::$app->request->get('id')])->one();
        if (!$item) return $this->fail(Yii::t('base',40001));

        $data = Tools::format_array($item->toArray(),['data'=>['json_decode',['###',true]]]);
        $data['paramsItems'] = Tools::format_array($item->paramsItems,['value'=>['json_decode',['###',true]]],2);
        $data['specItems'] = $item->specItems;

        return $this->success($data);
    }

    /**
     * Creates a new Type model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Type();

        $data = Yii::$app->request->post();
        if ($model->load($data,'') && $model->save()) {
            $this->setSpec($model->id,$data['specs']);
            $this->setParams($model->id,$data['params']);
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Type model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));

        $data = Yii::$app->request->post();
        if ($model->load($data,'') && $model->save()) {
            $this->setSpec($model->id,$data['specs']);
            $this->setParams($model->id,$data['params']);
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * 设置规格
     * @param  [int] $type_id 类型id
     * @param  [array] $items
     * @return void
     */
    protected function setSpec($type_id,$items=[])
    {
        TypeSpec::deleteAll(['type_id'=>$type_id]);
        foreach ($items as $k => $v) {
            if (empty($items[$k])) continue;
            $sv = new TypeSpec();
            $sv->load([
                'type_id'=>$type_id,
                'spec_id'=>$items[$k]
            ],'');
            $sv->save();
        }
    }

    /**
     * 设置参数
     * @param  [int] $type_id 类型id
     * @param  [array] $items
     * @return void
     */
    protected function setParams($type_id,$items=[])
    {
        TypeParams::deleteAll(['type_id'=>$type_id]);
        foreach ($items as $k => $v) {
            if (empty($items[$k])) continue;
            $sv = new TypeParams();
            $sv->load([
                'type_id'=>$type_id,
                'params_id'=>$items[$k]
            ],'');
            $sv->save();
        }
    }

    /**
     * Deletes an existing Type model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $item = $this->findModel($id);
        $transaction = Type::getDb()->beginTransaction();
        try {
            TypeParams::deleteAll(['type_id'=>$id]);
            TypeSpec::deleteAll(['type_id'=>$id]);
            $item->delete();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->success();
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Type::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(200,Yii::t('base',40001));
    }
}
