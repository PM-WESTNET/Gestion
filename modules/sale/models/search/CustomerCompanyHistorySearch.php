<?php

namespace app\modules\sale\models;

use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use app\modules\log\db\ActiveRecord;
use app\modules\westnet\models\Vendor;
use app\modules\sale\modules\contract\models\search\ContractSearch;

/**
 * This is the model class for table "customer_company_history".
 *
 * @property int $customer_company_history_id
 * @property int $customer_id
 * @property int $old_company_id
 * @property int $new_company_id
 * @property int $created_at
 *
 * @property Customer $customer
 * @property Company $newCompany
 * @property Company $oldCompany
 */
class CustomerCompanyHistorySearch extends ActiveRecord
{
 /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_class_id', 'code_ext', 'is_invoiced', 'tolerance_days', 'colour', 'percentage_bill', 'days_duration'], 'integer'],
            [['name'], 'safe'],
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
        

        $query = CustomerClass::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'customer_class_id' => $this->customer_class_id,
            'code_ext' => $this->code_ext,
            'is_invoiced' => $this->is_invoiced,
            'tolerance_days' => $this->tolerance_days,
            'colour' => $this->colour,
            'percentage_bill' => $this->percentage_bill,
            'days_duration' => $this->days_duration,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
