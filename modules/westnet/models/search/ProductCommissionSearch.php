<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\ProductCommission;

/**
 * ProductCommissionSearch represents the model behind the search form about `app\modules\westnet\models\ProductCommission`.
 */
class ProductCommissionSearch extends ProductCommission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_commission_id'], 'integer'],
            [['name'], 'safe'],
            [['percentage', 'value'], 'number'],
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
        $query = ProductCommission::find();

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
            'product_commission_id' => $this->product_commission_id,
            'percentage' => $this->percentage,
            'value' => $this->value,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
