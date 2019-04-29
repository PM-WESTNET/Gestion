<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Currency;

/**
 * CurrencySearch represents the model behind the search form about `app\modules\sale\models\Currency`.
 */
class CurrencySearch extends Currency
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_id'], 'integer'],
            [['name', 'iso', 'status', 'code'], 'safe'],
            [['rate'], 'number'],
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
        $query = Currency::find();

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
            'currency_id' => $this->currency_id,
            'rate' => $this->rate,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'iso', $this->iso])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
