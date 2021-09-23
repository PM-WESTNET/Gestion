<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\SiroPaymentIntentionValoration;

class SiroPaymentIntentionValorationSearch extends SiroPaymentIntentionValoration
{   

    public $from_date;
    public $to_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','email','description','created_at'],'safe'],
            [['siro_payment_intention_id'],'number']

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

        if(!empty($params['SiroPaymentIntentionValorationSearch']['from_date'])){
            $date = explode(' - ', $params['SiroPaymentIntentionSearch']['from_date']);

            $this->from_date = $date[0];
            $this->to_date = $date[1];
        }


        $query = SiroPaymentIntentionValoration::find()
        ->from('siro_payment_intention_valoration spiv')
        ->leftJoin('siro_payment_intention spi', 'spi.siro_payment_intention_id = spiv.siro_payment_intention_id')
        ->orderBy(['spiv.created_at' => SORT_DESC]);
 
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['status'] = [
            'asc' => ['spiv.status' => SORT_ASC],
            'desc' => ['spiv.status' => SORT_DESC],
        ];

        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'description', $this->description]);
        
        
        if($this->from_date){
            $query->andFilterWhere(['>=','created_at', $this->from_date]);
        }

        if($this->to_date){
            $query->andFilterWhere(['<=','created_at', $this->to_date]);
        }      
       
        return $dataProvider;
    }
}
