<?php

namespace app\modules\provider\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\provider\models\Provider;

/**
 * ProviderSearch represents the model behind the search form about `app\modules\provider\models\Provider`.
 */
class AccountSearch extends Provider
{
    
    public $fromDate;
    public $toDate;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_id'], 'integer'],
            [['fromDate', 'toDate'], 'safe'],
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
    
    public function search($params){
        
        $account = [];
        
        $bills = $this->searchBill($params)->getModels();
        $payments = $this->searchPayment($params)->getModels();
        
        foreach($bills as $bill){
            $account[strtotime($bill->date) + $bill->provider_bill_id] = $bill;
        }
        
        foreach($payments as $payment){
            $account[strtotime($payment->date) + $payment->provider_payment_id] = $payment;
        }
        
        return $account;
        
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchBill($params)
    {
        $query = ProviderBillSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>['date'=>SORT_ASC]
            ],
            'pagination'=>false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'provider_id' => $this->provider_id,
        ]);
        
//        $query->andWhere("date >= '$this->fromDate'");
  //      $query->andWhere("date <= '$this->toDate'");

        return $dataProvider;
    }
    
    public function searchPayment($params)
    {
        $query = ProviderPaymentSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>['date'=>SORT_ASC]
            ],
            'pagination'=>false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'provider_id' => $this->provider_id,
        ]);
        
    //    $query->andWhere("date >= '$this->fromDate'");
      //  $query->andWhere("date <= '$this->toDate'");

        return $dataProvider;
    }
}
