<?php

namespace app\modules\firstdata\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\firstdata\models\FirstdataAutomaticDebit;

/**
 * FirstdataAutomaticDebitSearch represents the model behind the search form of `app\modules\firstdata\models\FirstdataAutomaticDebit`.
 */
class FirstdataAutomaticDebitSearch extends FirstdataAutomaticDebit
{
    public $from_date;
    public $to_date;
    public $adhered_by;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_automatic_debit_id', 'customer_id', 'company_config_id'], 'integer'],
            [['from_date', 'to_date', 'user_id','created_at','adhered_by'], 'safe']
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
        $query = FirstdataAutomaticDebit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

         // grid filtering conditions
        $query->andFilterWhere([
            'firstdata_automatic_debit_id' => $this->firstdata_automatic_debit_id,
            'customer_id' => $this->customer_id,
            'company_config_id' => $this->company_config_id,
            'user_id' => $this->user_id,
        ]);

        if (!empty($this->from_date)) {
            $query->andWhere(['>=', 'created_at', strtotime(Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd'))]);
        }

        if (!empty($this->to_date)) {
            $query->andWhere(['<', 'created_at', (strtotime(Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd'))+86400)]);
        }

        $query->andFilterWhere([
            'adhered_by' => $this->adhered_by,
        ]);

        return $dataProvider;
    }
}
