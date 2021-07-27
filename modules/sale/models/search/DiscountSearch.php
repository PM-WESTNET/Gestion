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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount_id','customerAmount','value','code'], 'integer'],
            [['name', 'referenced'], 'safe'],
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
        $query->andFilterWhere(['like', 'chd.from_date', $this->from_date]);
        $query->andFilterWhere(['like', 'chd.to_date', $this->to_date]);

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

        // awesome query
        $query = Discount::find()
                ->select('COUNT(*) AS customerAmount, d.*')
                ->from('discount d')
                ->leftJoin('customer_has_discount chd', 'd.discount_id = chd.discount_id' )
                ->groupBy('d.discount_id')
                ->filterHaving(['like', 'COUNT(*)', $this->customerAmount])
                ;

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

        // standard Where equals
        $query->andFilterWhere([
            'discount_id' => $this->discount_id,
        ]);

        // AND WHERE LIKE-s (pattern match)
        $query->andFilterWhere(['like', 'd.name', $this->name]);
        $query->andFilterWhere(['like', 'd.status', $this->status]);
        $query->andFilterWhere(['like', 'd.from_date', $this->from_date]);
        $query->andFilterWhere(['like', 'd.to_date', $this->to_date]);
        $query->andFilterWhere(['like', 'd.type', $this->type]);
        $query->andFilterWhere(['like', 'd.value', $this->value]);
       
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
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
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
