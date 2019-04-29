<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Address;

/**
 * AddressSearch represents the model behind the search form about `app\modules\sale\models\Address`.
 */
class AddressSearch extends Address
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_id', 'number', 'floor', 'zone_id'], 'integer'],
            [['street', 'between_street_1', 'between_street_2', 'block', 'house', 'department', 'tower'], 'safe'],
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
        $query = Address::find();

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
            'address_id' => $this->address_id,
            'number' => $this->number,
            'floor' => $this->floor,
            'zone_id' => $this->zone_id,
        ]);

        $query->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'between_street_1', $this->between_street_1])
            ->andFilterWhere(['like', 'between_street_2', $this->between_street_2])
            ->andFilterWhere(['like', 'block', $this->block])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'tower', $this->tower]);

        return $dataProvider;
    }
}
