<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\IntegratechReceivedSms as IntegratechReceivedSmsModel;
use app\modules\westnet\notifications\NotificationsModule;

/**
 * IntegratechReceivedSms represents the model behind the search form of `app\modules\westnet\notifications\models\IntegratechReceivedSms`.
 */
class IntegratechReceivedSms extends IntegratechReceivedSmsModel
{
    public $fromDate;
    public $toDate;

    public function rules()
    {
        return [
            [['integratech_received_sms_id', 'ticket_id','customer_id'], 'integer'],
            [['destaddr', 'charcode', 'sourceaddr', 'message', 'datetime', 'customer_name', 'fromDate', 'toDate'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fromDate' => NotificationsModule::t('app', 'From date'),
            'toDate' => NotificationsModule::t('app', 'To date'),
            'message' => NotificationsModule::t('app', 'Message'),
            'customer_id' => NotificationsModule::t('app','Customer'),
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
        $query = IntegratechReceivedSmsModel::find();

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
            'integratech_received_sms_id' => $this->integratech_received_sms_id,
            'customer_id' => $this->customer_id,
            'ticket_id' => $this->ticket_id,
            'datetime' => $this->datetime,
        ]);

        $query->andFilterWhere(['like', 'destaddr', $this->destaddr])
            ->andFilterWhere(['like', 'charcode', $this->charcode])
            ->andFilterWhere(['like', 'sourceaddr', $this->sourceaddr])
            ->andFilterWhere(['like', 'message', $this->message]);

        if($this->fromDate && $this->toDate){
            $query->andFilterWhere(['between', 'datetime', $this->fromDate.' 00:00:00', $this->toDate.' 23:23:59']);
        } else {
            if($this->fromDate){
                $query->andFilterWhere(['>=', 'datetime', $this->fromDate.' 00:00:00']);
            }

            if($this->toDate){
                $query->andFilterWhere(['<=', 'datetime', $this->toDate.' 23:23:59']);
            }
        }

        return $dataProvider;
    }
}
