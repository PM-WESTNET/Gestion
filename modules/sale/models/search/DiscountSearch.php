<?php

namespace app\modules\sale\models\search;

use app\modules\sale\models\Discount;
use app\modules\sale\models\CustomerHasDiscount;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DocumentTypeSearch represents the model behind the search form about `app\modules\sale\models\DocumentType`.
 */
class DiscountSearch extends Discount
{   
    public $customer_has_discount_from_date;
    public $customer_has_discount_to_date;
    public $customerFilterRange; // this attr references the created_at field of the customer_has_discount table

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount_id','customerAmount','customersInsideRange','value','code'], 'integer'],
            [['name', 'referenced', 'from_date', 'to_date','customer_has_discount_from_date','customer_has_discount_to_date'], 'safe'],
            [['status'], 'string'], // recordar que status existe en dos tablas con el mismo nombre
            [['customerFilterRange'], 'string'], // recordar que status existe en dos tablas con el mismo nombre
            /* [['from_date', 'to_date', 'lastname'], 'string'], */ // mejorar implementacion de esto
            [['lastname'], 'string'], 
            [['referenced'], 'boolean'],
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
    public function searchCustomersOfDiscount($params)
    {
        
        $this->load($params);

        // awesome query
        $query = Discount::find()
                ->select('c.customer_id, c.name, c.lastname, c.code, chd.status, chd.from_date, chd.to_date')
                ->from('customer c')
                ->leftJoin('customer_has_discount chd', 'c.customer_id = chd.customer_id' )
                ->where(['chd.discount_id' => $params['discount_id']])
		->orderBy(['chd.from_date' => SORT_ASC])
                ;

        // creates the ActiveDataProvider instance
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->sort->defaultOrder = ['status' => SORT_ASC];

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'concat(concat(c.name," ",c.lastname), " ", c.code)', $this->name]);
        $query->andFilterWhere(['like', 'c.lastname', $this->lastname]);
        $query->andFilterWhere(['like', 'c.code', $this->code]);
        $query->andFilterWhere(['like', 'chd.status', $this->status]);

        $query->andFilterWhere(['>=', 'chd.from_date', $this->customer_has_discount_from_date]);
        $query->andFilterWhere(['<=', 'chd.from_date', $this->customer_has_discount_to_date]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchDiscounts($params)
    {   
        $this->load($params);

        // subquery to get the customers inside of the range in dates of adherence to a specific discount
        $subQuery = CustomerHasDiscount::find()
                    ->select('chd.discount_id, count(*) as customersInsideRange') // FROM_UNIXTIME(chd.created_at) as CreadoEn
                    ->from('customer_has_discount chd')
                    ;

        // filtering of the customers per date range of adherence
        $validDateRangeForCustomers = array('','');
        if(!empty($this->customerFilterRange)){
            $validDateRangeForCustomers = explode(' - ', $this->customerFilterRange);
            foreach($validDateRangeForCustomers as $i => $date){
                $validDateRangeForCustomers[$i] = strtotime($date); // we need it to be an unix timestamp int for the DB query
                // $validDateRangeForCustomers[$i] = date("Y-m-d", strtotime($date)); // we need it to be Y-m-d for the DB query
            }
            $subQuery->andFilterWhere(['>=', 'chd.created_at', $validDateRangeForCustomers[0]]);
            $subQuery->andFilterWhere(['<=', 'chd.created_at', $validDateRangeForCustomers[1]]);
        }
        $subQuery->groupBy('chd.discount_id');
        
        // awesome query
        $query = Discount::find()
                ->select(['COUNT(chd.customer_id) as customerAmount, d.*,ft.customersInsideRange'])
                ->from('discount d')
                ->leftJoin('customer_has_discount chd', 'd.discount_id = chd.discount_id')
                ->leftJoin(['ft' => $subQuery], 'd.discount_id = ft.discount_id')
                ->groupBy('d.discount_id')
                // ->filterHaving(['like', 'COUNT(*)', $this->customerAmount.'%', false])
                ;

        // standard Where equals
        $query->andFilterWhere(['discount_id' => $this->discount_id]);
        
        // Where's
        $query->andFilterWhere(['like', 'd.name', $this->name]); // by name
        $query->andFilterWhere(['like', 'd.status', $this->status]); // by status
        $query->andFilterWhere(['like', 'd.type', $this->type]); // by type
        $query->andFilterWhere(['like', 'd.value', $this->value.'%', false]); // by value
        
        // filtering of the discounts per date range of validity
        $validDateRangeForDiscounts = array('',''); // by default, date is empty, so that the query doesnt have any range constraints (and gets ALL discounts from the start)
        if(!empty($this->from_date)){
            $validDateRangeForDiscounts = explode(' - ', $this->from_date);

            foreach($validDateRangeForDiscounts as $i => $date){
                $validDateRangeForDiscounts[$i] = date("Y-m-d", strtotime($date)); // we need it to be Y-m-d for the DB query
            }
            // NEW DATE FILTER METHOD: https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap/
            // need to implement this logic rule for filtering (StartA <= EndB) and (EndA >= StartB) 
            // $validDateRangeForDiscounts[0] = StartA
            // $validDateRangeForDiscounts[1] = EndA
            // d.from_date = StartB
            // d.to_date = EndB
            $query->andFilterWhere(['>=', 'd.to_date', $validDateRangeForDiscounts[0]]);
            $query->andFilterWhere(['<=', 'd.from_date', $validDateRangeForDiscounts[1]]);
        }

        // creates the ActiveDataProvider instance
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        // Adds a custom attribute to the end of the attributes array
        $dataProvider->sort->attributes['customerAmount'] = [
            'asc' => ['customerAmount' => SORT_ASC],
            'desc' => ['customerAmount' => SORT_DESC],
        ];

        // Sets the defaultOrder for the Sort to DESC for the total quantity of clients associated to a discount
        $dataProvider->sort->defaultOrder = ['customerAmount' => SORT_DESC];

        if (!$this->validate()) {
            return $dataProvider;
        }
        //die(var_dump($query));

        return $dataProvider;
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

        $query = Discount::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'discount_id' => $this->discount_id,
            'status' => $this->status,
            'value' => $this->value,
            'type' => $this->type
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
