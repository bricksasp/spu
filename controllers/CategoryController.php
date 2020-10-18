<?php
namespace bricksasp\spu\controllers;

use Yii;
use bricksasp\spu\models\Category;
use bricksasp\spu\models\CategorySearch;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use bricksasp\helpers\Tools;
use bricksasp\rbac\models\redis\Token;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{
    /**
     * 免登录可访问
     * @return array
     */
    public function allowNoLoginAction()
    {
        return [
            'view',
            'index',
            'tree',
            'view',
        ];
    }

    /**
     * Lists all Type models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search($this->queryFilters());
        return $this->pageFormat($dataProvider,['image'=>[['file_url'=>['implode',['',[\bricksasp\base\Config::instance()->web_url,'###']],'array']]]]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView()
    {
        $model = $this->findModel(Yii::$app->request->get('id'));
        $data = $model->toArray();
        $data['image'] = $model['image'] ? Tools::format_array($model['image'],['file_url'=>['implode',['',[\bricksasp\base\Config::instance()->web_url,'###']],'array']]) : (object)[];
        $data['parent_id'] = $data['parent_id'] ? [(string)$data['parent_id']] : [];
        return $this->success($data);
    }

    /**
     * 获取分类树
     * @OA\Get(path="/spu/category/tree",
     *   summary="商品分类",
     *   tags={"spu模块"},
     *   @OA\Parameter(
     *     description="开启平台功能后，访问商户对应的数据标识，未开启忽略此参数",
     *     name="access-token",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="列表数据",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/goodsCatList"),
     *     ),
     *   ),
     * )
     *
     * @OA\Schema(
     *   schema="goodsCatList",
     *   description="分类列表结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="id", type="integer", description="分类id"),
     *       @OA\Property(property="name", type="string", description="分类名称"),
     *       @OA\Property( property="image", description="售价", ref="#/components/schemas/file"),
     *       @OA\Property( property="children", type="array", description="子集", @OA\Items(
     *           @OA\Property(property="name", type="string", description="分类名称"),
     *           @OA\Property( property="image", description="售价", ref="#/components/schemas/file"),
     *         )
     *       ),
     *     )
     *   }
     * )
     */
    public function actionTree($id=null)
    {
        $map = [];
        if ($id) {
            $map = ['!=', 'id', (int)$id];
        }
        $query = Category::find($this->dataOwnerUid())->select(['id', 'parent_id', 'name', 'image_id']);
        $data = $query->with(['image'])->andWhere($map)->all();
        foreach ($data as $key => $item) {
            $data[$key] = $item->toArray();
            $data[$key]['image'] = $item->image ? Tools::format_array($item->image,['file_url'=>['implode',['',[\bricksasp\base\Config::instance()->web_url,'###']],'array']]) : '';
        }

        $tree = Tools::build_tree($data);
        return $this->success($tree);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        $data = Yii::$app->request->post();
        if (!empty($data['parent_id']) && is_array($data['parent_id'])) {
            $data['parent_id'] = end($data['parent_id']);
        }
        if ($model->load($data) && $model->save()) {
            return $this->success($model);
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));

        $data = Yii::$app->request->post();
        if (!empty($data['parent_id']) && is_array($data['parent_id'])) {
            $data['parent_id'] = end($data['parent_id']);
        }
        if ($model->load($data,'') && $model->save()) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));
        $this->deleteChildren($model->id);
        return $model->delete() !== false ? $this->success() : $this->fail();
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(200,Yii::t('base',40001));
    }

    public function deleteChildren($id)
    {
        $children = Category::find()->where(['parent_id'=>$id])->asArray()->all();
        if ($children) {
            $ids = array_column($children,'id');
            Category::deleteAll(['id'=>$ids]);
            $this->deleteChildren($ids);
        }
        return true;
    }
}
