<?php

namespace app\modules\provider\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\provider\models\ProviderBill;

/**
 * ProviderBillSearch represents the model behind the search form about `app\modules\provider\models\ProviderBill`.
 */
class ProviderBillSearch extends ProviderBill
{

    public $start_date;
    public $finish_date;
    public $company_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_bill_id', 'provider_id'], 'integer'],
            [['date', 'type', 'number', 'description', 'start_date', 'finish_date', 'company_id', 'bill_type_id'], 'safe'],
            [['net', 'taxes', 'total'], 'number'],
        ];
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
                'start_date' => Yii::t('app', 'Start Date'),
                'finish_date'=> Yii::t('app', 'Finish Date'),
                'company_id'=> Yii::t('app', 'Company'),
            ]);
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
        $query = ProviderBill::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->leftJoin('company comp', 'comp.company_id = provider_bill.company_id');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->filterByCompany($query);
        $this->filterByDates($query);

        $query->andFilterWhere([
            'provider_id' => $this->provider_id,
            'bill_type_id'=>$this->bill_type_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'description', $this->description]);

        $query->orderBy(['timestamp' => SORT_DESC]);

        return $dataProvider;
    }


    private function filterByCompany($query){
        if (!empty($this->company_id)) {
            $query->andFilterWhere(['comp.company_id' => $this->company_id]);
        }
    }

    private function filterByDates($query){
        if (!empty($this->start_date)) {
            $query->andFilterWhere(['>=', 'date', Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd')]);
        }

        if (!empty($this->finish_date)) {
            $query->andFilterWhere(['<=', 'date', Yii::$app->formatter->asDate($this->finish_date, 'yyyy-MM-dd')]);
        }
    }
}