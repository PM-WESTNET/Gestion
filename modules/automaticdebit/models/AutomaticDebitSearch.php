<?php

namespace app\modules\automaticdebit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\automaticdebit\models\AutomaticDebit;

/**
 * AutomaticDebitSearch represents the model behind the search form of `app\modules\automaticdebit\models\AutomaticDebit`.
 */
class AutomaticDebitSearch extends AutomaticDebit
{
    public $company_name;
    public $user_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['automatic_debit_id', 'customer_id', 'bank_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cbu', 'beneficiario_number', 'company_name'], 'safe'],
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
        $query = AutomaticDebit::find();
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['automatic_debit_id'=>SORT_DESC]],
        ]);
        $query->joinWith(['customer', 'customer.company']);

        $this->load($params);

        

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }
        /* var_dump($params);
        die(); */
        // grid filtering conditions
        $query->andFilterWhere([
            'automatic_debit_id' => $this->automatic_debit_id,
            'automatic_debit.customer_id' => $this->customer_id,
            'bank_id' => $this->bank_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ])  ->andFilterWhere(['like', 'cbu', $this->cbu])
            ->andFilterWhere(['like', 'beneficiario_number', $this->beneficiario_number])
            ->andFilterWhere(['like', 'company.name', $this->company_name]);

        if (isset($params['AutomaticDebitSearch']['user_id'])){
            $query->andFilterWhere(['user_id' => $params['AutomaticDebitSearch']['user_id']]);
        }
            
        return $dataProvider;
    }
}
