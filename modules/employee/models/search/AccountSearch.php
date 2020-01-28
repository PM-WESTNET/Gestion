<?php

namespace app\modules\employee\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\Employee;

/**
 * EmployeeSearch represents the model behind the search form about `app\modules\employee\models\Employee`.
 */
class AccountSearch extends Employee
{
    
    public $fromDate;
    public $toDate;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id'], 'integer'],
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
            $account[strtotime($bill->date) + $bill->employee_bill_id] = $bill;
        }
        
        foreach($payments as $payment){
            $account[strtotime($payment->date) + $payment->employee_payment_id] = $payment;
        }
        
        return $account;
        
    }

    /**
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchBill($params)
    {
        $query = EmployeeBillSearch::find();

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
            'employee_id' => $this->employee_id,
        ]);
        
//        $query->andWhere("date >= '$this->fromDate'");
  //      $query->andWhere("date <= '$this->toDate'");

        return $dataProvider;
    }
    
    public function searchPayment($params)
    {
        $query = EmployeePaymentSearch::find();

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
            'employee_id' => $this->employee_id,
        ]);
        
    //    $query->andWhere("date >= '$this->fromDate'");
      //  $query->andWhere("date <= '$this->toDate'");

        return $dataProvider;
    }
}
