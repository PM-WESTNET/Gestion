<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\TaxCondition;

/**
 * TaxConditionSearch represents the model behind the search form about `app\modules\sale\models\TaxCondition`.
 */
class TaxConditionSearch extends TaxCondition
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_condition_id'], 'integer'],
            [['name'], 'safe'],
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
        $query = TaxCondition::find();
        
        $query->leftJoin('tax_condition_has_document_type tchdt', 'tchdt.tax_condition_id = tax_condition.tax_condition_id');

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
            'tax_condition_id' => $this->tax_condition_id,
            'tchdt.document_type_document_type_id' => $this->_documentTypes,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
