<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\TaxRate;

/**
 * TaxRateSearch represents the model behind the search form about `app\modules\sale\models\TaxRate`.
 */
class TaxRateSearch extends TaxRate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_rate_id', 'tax_id'], 'integer'],
            [['pct'], 'safe'],
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
        $query = TaxRate::find();

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
            'tax_rate_id' => $this->tax_rate_id,
            'tax_id' => $this->tax_id,
        ]);

        $query->andFilterWhere(['pct' => $this->pct]);
        
        $query->orderBy(['pct' => SORT_ASC]);

        return $dataProvider;
    }
}
