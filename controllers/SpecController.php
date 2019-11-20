<?php
namespace bricksasp\spu\controllers;

use Yii;
use bricksasp\spu\models\Spec;
use bricksasp\spu\models\SpecValue;
use yii\data\ActiveDataProvider;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * SpecController implements the CRUD actions for Spec model.
 */
class SpecController extends BaseController
{
    /**
     * 登录可访问 其他需授权
     * @return array
     */
    public function allowAction()
    {
        return [
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
            'query' => Spec::find($this->dataOwnerUid()),
            'pagination' => [
                'pageSize' => $params['pageSize'] ?? 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC
                ]
            ],
        ]);
        return $this->pageFormat($dataProvider);
    }

    /**
     * Displays a single Spec model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $item = Spec::find()->with(['items'])->where(['id'=> Yii::$app->request->get('id')])->one();
        if (!$item) return $this->fail(Yii::t('spu',40001));
        $data = $item->toArray();
        $data['items'] = $item->items;
        return $this->success($data);
    }

    /**
     * Creates a new Spec model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Spec();
        $data = Yii::$app->request->post();
        if ($model->load($data,'') && $model->save()) {
            SpecValue::deleteAll(['spec_id'=>$model->id]);
            foreach ($data['items'] as $row) {
                $row['spec_id'] = $model->id;
                $sv = new SpecValue();
                $sv->load($row,'');
                $sv->save();
            }
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Spec model.
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
            SpecValue::deleteAll(['spec_id'=>$model->id]);
            foreach ($data['items'] as $row) {
                $row['spec_id'] = $model->id;
                $sv = new SpecValue();
                $sv->load($row,'');
                $sv->save();
            }
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Deletes an existing Spec model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));
        if ($model->delete()) {
            SpecValue::deleteAll(['spec_id'=>$model->id]);
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * Finds the Spec model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Spec the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Spec::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(200,Yii::t('base',40001));
    }
}
