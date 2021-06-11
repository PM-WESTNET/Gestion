<?php
namespace app\modules\westnet\reports\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\reports\models\ReportChangeCompany;
use Yii;

/**
 * ReportChangeCompanySearch represents the model behind the search form of `app\modules\westnet\reports\models\ReportChangeCompany`.
 */
class ReportChangeCompanySearch extends ReportChangeCompany
{

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['customer.name','customer.code']);
    }

    public function rules()
    {
        return [
            [['id_report_change_company','customer_id_customer','customer.code'], 'integer'],
            [['date','date2'], 'safe'],
            [['new_business_name', 'old_business_name','customer.name'], 'string'],
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

        $query = ReportChangeCompany::find();
        $query->joinWith(['customer']);

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
            'id_report_change_company' => $this->id_report_change_company,
        ]);

        $query->andFilterWhere(['like', 'customer.code', $this->getAttribute('customer.code')])
        ->andFilterWhere(['like', 'customer.name', $this->getAttribute('customer.name')])
        ->andFilterWhere(['like', 'new_business_name', $this->new_business_name])
        ->andFilterWhere(['like', 'old_business_name', $this->old_business_name])
        ->andFilterWhere(['like', 'date', $this->date]);
            

         /* if($this->start_date && $this->start_date_2){
            $query  ->andFilterWhere([">=", 'DATE_FORMAT(formalities.start_date,"%Y-%m-%d")', $this->start_date])
                    ->andFilterWhere(["<=", 'DATE_FORMAT(formalities.start_date,"%Y-%m-%d")', $this->start_date_2]);
        }else{
            $query->andFilterWhere(['like','formalities.start_date', $this->start_date]);
        }

        if($this->tray_start_date && $this->tray_start_date_2){
            $query->andWhere('idformality in (SELECT formality_idformality from formalities_has_trays WHERE DATE_FORMAT(start_date,"%Y-%m-%d") BETWEEN "'.$this->tray_start_date.'" and "'.$this->tray_start_date_2.'" and end_date is null)');
        }else if($this->tray_start_date && isnull($this->tray_start_date_2)){
            $query->andWhere('idformality in (SELECT formality_idformality from formalities_has_trays WHERE start_date LIKE "'.$this->tray_start_date.'" and end_date is null)');
        }*/   
        return $dataProvider;
    }
}
