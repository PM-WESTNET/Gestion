<?php

namespace app\modules\sale\models\search;

use app\modules\sale\models\Discount;
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount_id','customerAmount','value','code'], 'integer'],
            [['name', 'referenced', 'from_date', 'to_date','customer_has_discount_from_date','customer_has_discount_to_date'], 'safe'],
            [['status'], 'string'], // recordar que status existe en dos tablas con el mismo nombre
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
        $validDateRange = array('',''); // by default, date is empty, so that the query doesnt have any range constraints (and gets ALL discounts from the start)

        // awesome query
        $query = Discount::find()
                ->select('COUNT(*) AS customerAmount, d.*')
                ->from('discount d')
                ->leftJoin('customer_has_discount chd', 'd.discount_id = chd.discount_id' )
                ->groupBy('d.discount_id')
                ->filterHaving(['like', 'COUNT(*)', $this->customerAmount.'%', false])
                ;

        // standard Where equals
        $query->andFilterWhere([
            'discount_id' => $this->discount_id,
        ]);
        
        if(!empty($this->from_date)){
            $validDateRange = explode(' - ', $this->from_date);

            foreach($validDateRange as $i => $date){
                $validDateRange[$i] = date("Y-m-d", strtotime($validDateRange[$i])); // we need it to be Y-m-d for the DB query
            }
        }
        
        // Where's
        $query->andFilterWhere(['like', 'd.name', $this->name]); // by name
        $query->andFilterWhere(['like', 'd.status', $this->status]); // by status
        $query->andFilterWhere(['like', 'd.type', $this->type]); // by type
        $query->andFilterWhere(['like', 'd.value', $this->value.'%', false]); // by value
        
        // NEW DATE FILTER METHOD: https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap/
        // need to implement this logic rule for filtering (StartA <= EndB) and (EndA >= StartB) 
        // $validDateRange[0] = StartA
        // $validDateRange[1] = EndA
        // d.from_date = StartB
        // d.to_date = EndB
        $query->andFilterWhere(['>=', 'd.to_date', $validDateRange[0]]);
        $query->andFilterWhere(['<=', 'd.from_date', $validDateRange[1]]);


        // creates the ActiveDataProvider instance
        $dataProvider = new ActiveDataProvider([
        'query' => $query
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
