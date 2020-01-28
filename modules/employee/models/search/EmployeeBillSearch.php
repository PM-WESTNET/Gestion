<?php

namespace app\modules\employee\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\EmployeeBill;
use yii\db\Expression;

/**
 * EmployeeBillSearch represents the model behind the search form about `app\modules\employee\models\EmployeeBill`.
 */
class EmployeeBillSearch extends EmployeeBill
{

    public $start_date;
    public $finish_date;
    public $company_id;
    public $amountApplied;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_bill_id', 'employee_id'], 'integer'],
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
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EmployeeBill::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->leftJoin('company comp', 'comp.company_id = employee_bill.company_id');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->filterByCompany($query);
        $this->filterByDates($query);

        $query->andFilterWhere([
            'employee_id' => $this->employee_id,
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

    public function searchWithDebt($params)
    {
        /** @var Query $query */
        $query = $this->find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['timestamp' => SORT_ASC]
            ]
        ]);

        $query->addSelect(['employee_bill.*', 'sum(coalesce(pbhpp.amount, 0)) as amountApplied'])
            ->joinWith(['billType'])
            ->leftJoin('employee_bill_has_employee_payment pbhpp', 'employee_bill.employee_bill_id = pbhpp.employee_bill_id')
            ->where(['or',
                ['pbhpp.employee_bill_id' => null],
                ['and',
                    ['not', ['pbhpp.employee_bill_id' => null]],
                    ['<','pbhpp.amount', (new Expression('`employee_bill`.`total`'))]
                ]
            ])
            ;

        $this->load($params);

        if (!$this->validate()) {
            $query->where('1=2');
            return $dataProvider;
        }

        if($this->employee_id) {
            $query->andFilterWhere([
                'employee_bill.employee_id' => $this->employee_id,
                'bill_type.multiplier' => [-1, 1]
            ]);
        }

        //Fix pagination
        $query->groupBy('employee_bill.employee_bill_id');
        $query->having("sum(coalesce(pbhpp.amount, 0)) < employee_bill.total");

        return $dataEmployee;
    }
}