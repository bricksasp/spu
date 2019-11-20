<?php
namespace bricksasp\spu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use bricksasp\spu\models\Category;

/**
 * CategorySearch represents the model behind the search form of `bricksasp\spu\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'type_id', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['name', 'image_id'], 'safe'],
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
     * @param array $filters
     *
     * @return ActiveDataProvider
     */
    public function search($filters=[])
    {
        $map = [];
        if (!empty($filters['user_id'])) {
            $map['user_id'] = $filters['user_id'];
        }
        $query = Category::find($map)->with(['image']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $filters['pageSize'] ?? 10,
            ],
        ]);
        $this->load($filters,'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'image_id', $this->image_id]);

        return $dataProvider;
    }
}
