<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\PointOfSale;

/**
 * PointOfSaleSearch represents the model behind the search form about `app\modules\sale\models\PointOfSale`.
 */
class PointOfSaleSearch extends PointOfSale
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['point_of_sale_id', 'number', 'company_id', 'default'], 'integer'],
            [['name', 'status', 'description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
    public function search($params)
    {
        $query = PointOfSale::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'point_of_sale_id' => $this->point_of_sale_id,
            'number' => $this->number,
            'company_id' => $this->company_id,
            'default' => $this->default,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
