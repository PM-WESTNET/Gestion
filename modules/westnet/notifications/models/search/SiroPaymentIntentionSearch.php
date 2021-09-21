<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\SiroPaymentIntention;

class SiroPaymentIntentionSearch extends SiroPaymentIntention
{   

    public $customer;
    public $company;
    public $from_date;
    public $to_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer','status','estado','company','createdAt','updatedAt', 'from_date', 'to_date'],'safe'],
            [['payment_id'],'number']

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

        $this->load($params);

        if(!empty($params['SiroPaymentIntentionSearch']['from_date'])){
            $date = explode(' - ', $params['SiroPaymentIntentionSearch']['from_date']);

            $this->from_date = $date[0];
            $this->to_date = $date[1];
        }


        $query = SiroPaymentIntention::find()
        ->select(['spi.*'])
        ->from('siro_payment_intention spi')
        ->leftJoin('customer c', 'c.customer_id = spi.customer_id')
        ->leftJoin('company com', 'com.company_id = spi.company_id')
        ->orderBy(['spi.createdAt' => SORT_DESC]);
 
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['status'] = [
            'asc' => ['spi.status' => SORT_ASC],
            'desc' => ['spi.status' => SORT_DESC],
        ];

        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'spi.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'concat(concat(c.lastname," ",c.name), " ", c.code)', $this->customer])
              ->andFilterWhere(['like', 'estado', $this->estado])
              ->andFilterWhere(['like', 'com.name', $this->company])
              ->andFilterWhere(['like', 'payment_id', $this->payment_id]);
        
        
        if($this->from_date){
            $query->andFilterWhere(['>=','createdAt', $this->from_date]);
        }

        if($this->to_date){
            $query->andFilterWhere(['<=','createdAt', $this->to_date]);
        }      
       
        return $dataProvider;
    }
}
