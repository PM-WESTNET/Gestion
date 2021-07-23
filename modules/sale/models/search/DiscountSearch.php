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
            [['discount_id'], 'integer'],
            [['value'], 'integer'],
            [['name', 'referenced'], 'safe'],
            [['status'], 'string'], // recordar que status existe en dos tablas con el mismo nombre
            [['from_date', 'to_date'], 'string'], // mejorar implementacion de esto
            [['referenced'], 'boolean'],
            [['customerAmount'], 'integer'],
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
        $this->load($params);

        $query = Discount::find()
                ->select('COUNT(*) AS customerAmount, d.*')
                ->from('discount d')
                ->leftJoin('customer_has_discount chd', 'd.discount_id = chd.discount_id' )
                ->groupBy('d.discount_id')
                ->filterHaving(['like', 'COUNT(*)', $this->customerAmount])
                ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => ['customerAmount'],
                'defaultOrder' => ['customerAmount' => SORT_DESC]
            ],
        ]);
        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /* $dataProvider->sort->attributes['customerAmount'] = [

            'asc' => ['customerAmount' => SORT_ASC],
            'desc' => ['customerAmount' => SORT_DESC],
        ];  */

        $query->andFilterWhere([
            'discount_id' => $this->discount_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'status', $this->status]);
        $query->andFilterWhere(['like', 'from_date', $this->from_date]);
        $query->andFilterWhere(['like', 'to_date', $this->to_date]);
        $query->andFilterWhere(['like', 'type', $this->type]);
        $query->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
