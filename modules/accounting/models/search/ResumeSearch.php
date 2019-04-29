<?php

namespace app\modules\accounting\models\search;

use app\modules\accounting\models\Resume;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Created by PhpStorm.
 * User: cgarcia
 */
class ResumeSearch extends Resume
{
    public $company_id;

    // Fechas
    public $toDate;
    public $fromDate;

    // Estados de movimiento
    public $statuses;

    public $money_box_id;

    //public $money_box_account_id;

    public function rules()
    {
        $statuses = [Resume::STATE_DRAFT,Resume::STATE_CLOSED,Resume::STATE_CONCILED,Resume::STATE_CANCELED];

        return [
            [['company_id', 'money_box_id', 'money_box_account_id'], 'integer'],
            [['toDate', 'fromDate'], 'safe'],
            [['toDate', 'fromDate'], 'default', 'value'=>null],
            [['status'], 'in', 'range' => $statuses],
            ['statuses', 'each', 'rule' => ['in', 'range' => $statuses]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'statuses' => Yii::t('app', 'Statuses'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'account_id_from' => Yii::t('accounting', 'Account Id From'),
            'account_id_to' => Yii::t('accounting', 'Account Id To'),
        ]);
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function init()
    {
        parent::init();
    }


    /**
     * Busqueda regular
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $query = Resume::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date'=>SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->filterDates($query);
        $this->filterStatus($query);
        $this->filterCompany($query);
        $this->filterMoneyBox($query);
        $this->filterMoneyBoxAccount($query);
        return $dataProvider;
    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterStatus($query){

        if(!empty($this->statuses)){
            $query->andFilterWhere(["resume.status"=>$this->statuses]);
        } else {
            if($this->status!==null) {
                $query->andFilterWhere(["resume.status"=>$this->status]);
            }
        }
    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterDates($query)
    {
        if(!empty($this->fromDate)){
            $query->andFilterWhere(['>', 'date', $this->fromDate]);
        }
        if(!empty($this->toDate)){
            $query->andFilterWhere(['<', 'date', $this->toDate]);
        }
    }

    /**
     * Agrega queries para filtrar por company
     * @param type $query
     */
    private function filterCompany($query)
    {
        if(!empty($this->company_id)){
            $query->andFilterWhere(['resume.company_id'=>$this->company_id]);
        }
    }


    /**
     * Agrega queries para filtrar por company
     * @param type $query
     */
    private function filterMoneyBox($query)
    {
        if($this->money_box_account_id == 0 && $this->money_box_id != 0){
            $query->leftJoin('money_box_account mba', 'resume.money_box_account_id = mba.money_box_account_id');
            $query->andFilterWhere(['mba.money_box_id'=>$this->money_box_id]);
        }
    }

    /**
     * Agrega queries para filtrar por company
     * @param type $query
     */
    private function filterMoneyBoxAccount($query)
    {
        if($this->money_box_account_id!=0){
            $query->andFilterWhere(['resume.money_box_account_id'=>$this->money_box_account_id]);
        }
    }
}