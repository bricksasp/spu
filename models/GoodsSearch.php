<?php
namespace bricksasp\spu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use bricksasp\spu\models\Goods;
use bricksasp\helpers\Tools;

/**
 * GoodsSearch represents the model behind the search form of `bricksasp\spu\models\Goods`.
 */
class GoodsSearch extends Goods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'cat_id', 'type_id', 'brand_id', 'is_nomal_virtual', 'is_on_shelves', 'on_shelves_time', 'off_shelves_time', 'comments_count', 'view_count', 'buy_count', 'sell_count', 'sort', 'is_recommend', 'is_hot', 'status', 'user_id', 'version', 'created_at', 'updated_at'], 'integer'],
            [['content', 'specs', 'params'], 'string'],
            [['gn'], 'string', 'max' => 30],
            [['name', 'brief', 'keywords'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params=[])
    {
        $query = Goods::find()/*->select(['id','name','gn'])*/->with(['coverItem','labelItems']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $params['pageSize'] ?? 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            Tools::exceptionBreak(Yii::t('base',50006));
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cat_id' => $this->cat_id,
            'user_id' => $this->user_id,
            'brand_id' => $this->brand_id,
            'is_nomal_virtual' => $this->is_nomal_virtual,
            'is_on_shelves' => $this->is_on_shelves,
            'is_recommend' => $this->is_recommend,
            'is_hot' => $this->is_hot,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'gn', $this->gn])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'brief', $this->brief])
            ->andFilterWhere(['like', 'keywords', $this->keywords]);

        return $dataProvider;
    }
}
