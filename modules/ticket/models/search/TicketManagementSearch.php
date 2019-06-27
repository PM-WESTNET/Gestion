<?php

namespace app\modules\ticket\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ticket\models\TicketManagement;

/**
 * TicketManagementSearch represents the model behind the search form of `app\modules\ticket\models\TicketManagement`.
 */
class TicketManagementSearch extends TicketManagement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_management_id', 'ticket_id', 'user_id'], 'integer'],
            [['date'], 'safe'],
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
    public function search($params)
    {
        $query = TicketManagement::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ticket_management_id' => $this->ticket_management_id,
            'ticket_id' => $this->ticket_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
