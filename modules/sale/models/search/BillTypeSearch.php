<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\BillType;
use yii\db\Query;

/**
 * BillTypeSearch represents the model behind the search form about `app\modules\sale\models\BillType`.
 */
class BillTypeSearch extends BillType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bill_type_id', 'code'], 'integer'],
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
        $query = BillType::find();

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
            'bill_type_id' => $this->bill_type_id,
            'code' => $this->code,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * Busca un tipo de comprobante dependiendo de la condicion de impuesto del customer
     * y company.
     *
     * @param $class
     * @param $tax_condition
     * @param $company_id
     */
    public function searchForCustomer($class, $customer_id, $company_id)
    {
        /** @var Query $query */
        $query = BillType::find();
        $query->leftJoin('company_has_bill_type cbt', 'bill_type.bill_type_id = cbt.bill_type_id')
            ->leftJoin('tax_condition_has_bill_type tcb', 'tcb.bill_type_id = bill_type.bill_type_id')
            ->leftJoin('customer c', 'c.tax_condition_id = tcb.tax_condition_id');

        $query->where([
            'class'=> $class,
            'c.customer_id' => $customer_id,
            'cbt.company_id' => $company_id
        ]);

        return $query->one();
    }
}
