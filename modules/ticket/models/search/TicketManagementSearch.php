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

    public $customer_id;
    public $date_from;
    public $date_to;

    public function rules()
    {
        return [
            [['ticket_management_id', 'ticket_id', 'user_id', 'customer_id'], 'integer'],
            [['date_from', 'date_to'], 'safe'],
            [['by_wp', 'by_call', 'by_email', 'by_sms'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date_from' => Yii::t('app', 'Date from'),
            'date_to' => Yii::t('app', 'Date to')
        ]);
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

        if($this->customer_id) {
            $query->leftJoin('ticket', 'ticket.ticket_id = ticket_management.ticket_id')
                ->where(['ticket.customer_id' => $this->customer_id]);
        }

        if($this->date_from) {
            $date_from_timestamp = (new \DateTime($this->date_from . '00:00:00'))->getTimestamp();
            $query->andFilterWhere(['>=','timestamp', $date_from_timestamp]);
        }

        if($this->date_to) {
            $date_to_timestamp = (new \DateTime($this->date_to . '23:59:59'))->getTimestamp();
            $query->andFilterWhere(['<=','timestamp', $date_to_timestamp]);
        }

        if($this->by_wp) {
            $query->andFilterWhere(['by_wp' => 1]);
        }

        if($this->by_call) {
            $query->andFilterWhere(['by_call' => 1]);
        }

        if($this->by_email) {
            $query->andFilterWhere(['by_email' => 1]);
        }

        if($this->by_sms) {
            $query->andFilterWhere(['by_sms' => 1]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'ticket_management_id' => $this->ticket_management_id,
            'ticket_id' => $this->ticket_id,
            'ticket_management.user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}
