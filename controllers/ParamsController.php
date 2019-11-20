<?php
namespace bricksasp\spu\controllers;

use Yii;
use bricksasp\spu\models\Params;
use yii\data\ActiveDataProvider;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use bricksasp\helpers\Tools;

/**
 * ParamsController implements the CRUD actions for Params model.
 */
class ParamsController extends BaseController
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
            'query' => Params::find($this->dataOwnerUid()),
            'pagination' => [
                'pageSize' => $params['limit'] ?? 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC
                ]
            ],
        ]);
        return $this->pageFormat($dataProvider,['value'=>['json_decode',['###',true]]],1);
    }

    /**
     * Displays a single Params model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $data = Tools::format_array($this->findModel(Yii::$app->request->get('id')),['value'=>['json_decode',['###',true]]]);
        return $this->success($data);
    }

    /**
     * Creates a new Params model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Params();

        if ($model->load(Yii::$app->request->post(),'') && $model->save()) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Params model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));

        if ($model->load(Yii::$app->request->post(),'') && $model->save()) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Deletes an existing Params model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete()
    {
        return $this->findModel(Yii::$app->request->post('id'))->delete() !== false ? $this->success() : $this->fail();
    }

    /**
     * Finds the Params model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Params the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Params::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(200,Yii::t('base',40001));
    }
}
